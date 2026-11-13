<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ReconcileAccountsCommand extends Command
{
    protected $signature = 'accounts:reconcile
        {--force-password : Make this app the password source for matching accounts}
        {--dry-run : Only show how many active accounts would be sent}';

    protected $description = 'Reconcile active user accounts with the configured CineWorm partner app';

    public function handle(): int
    {
        if (!config('services.account_sync.enabled')) {
            $this->error('Account sync is disabled.');
            return 1;
        }

        $partnerUrl = rtrim((string) config('services.account_sync.partner_url'), '/');
        $secret = (string) config('services.account_sync.secret');
        if ($partnerUrl === '' || $secret === '') {
            $this->error('Account sync partner URL or secret is missing.');
            return 1;
        }

        $payloads = User::query()->whereNotNull('email')->get()->map(function (User $user): array {
            return [
                'email' => strtolower(trim((string) $user->email)),
                'name' => (string) ($user->name ?? ''),
                'password_hash' => (string) $user->getAuthPassword(),
                'reconcile' => true,
                'force_password' => (bool) $this->option('force-password'),
                'restore_deleted' => true,
            ];
        })->filter(function (array $account): bool {
            return $account['email'] !== '' && !str_contains($account['email'], '__deleted__');
        })->values()->all();

        $this->info('Found ' . count($payloads) . ' active accounts.');
        if ($this->option('dry-run')) {
            return 0;
        }

        $summary = ['created' => 0, 'updated' => 0, 'restored' => 0, 'unchanged' => 0, 'invalid' => 0, 'failed' => 0];

        foreach (array_chunk($payloads, 200) as $batch) {
            $body = json_encode(['accounts' => $batch], JSON_UNESCAPED_SLASHES);

            try {
                $client = Http::timeout(30)
                    ->connectTimeout(8)
                    ->when(config('services.account_sync.partner_resolve'), function ($request) {
                        return $request->withOptions([
                            'curl' => [CURLOPT_RESOLVE => [config('services.account_sync.partner_resolve')]],
                        ]);
                    })
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-Account-Sync-Signature' => hash_hmac('sha256', $body, $secret),
                    ])
                    ->withBody($body, 'application/json');

                $response = $client->post($partnerUrl . '/api/internal/account-sync-batch');
                if (!$response->successful()) {
                    $summary['failed'] += count($batch);
                    $this->warn('Batch failed: HTTP ' . $response->status());
                    continue;
                }

                foreach (['created', 'updated', 'restored', 'unchanged', 'invalid'] as $key) {
                    $summary[$key] += (int) ($response->json($key) ?? 0);
                }
            } catch (\Throwable $e) {
                $summary['failed'] += count($batch);
                $this->warn('Batch failed: ' . $e->getMessage());
            }
        }

        $this->line(sprintf(
            'Created: %d; updated: %d; restored: %d; unchanged: %d; invalid: %d; failed: %d',
            $summary['created'], $summary['updated'], $summary['restored'],
            $summary['unchanged'], $summary['invalid'], $summary['failed']
        ));

        return $summary['failed'] > 0 ? 1 : 0;
    }
}
