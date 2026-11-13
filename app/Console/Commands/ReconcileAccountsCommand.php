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

        $users = User::query()->whereNotNull('email')->get();
        $this->info('Found ' . $users->count() . ' active accounts.');

        if ($this->option('dry-run')) {
            return 0;
        }

        $created = 0;
        $updated = 0;
        $restored = 0;
        $unchanged = 0;
        $failed = 0;

        foreach ($users as $user) {
            $email = strtolower(trim((string) $user->email));
            if ($email === '' || str_contains($email, '__deleted__')) {
                continue;
            }

            $payload = [
                'email' => $email,
                'name' => (string) ($user->name ?? ''),
                'password_hash' => (string) $user->getAuthPassword(),
                'reconcile' => true,
                'force_password' => (bool) $this->option('force-password'),
                'restore_deleted' => true,
            ];
            $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

            try {
                $client = Http::timeout(15)
                    ->connectTimeout(5)
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

                $response = $client->post($partnerUrl . '/api/internal/account-sync');

                if (!$response->successful()) {
                    $failed++;
                    $this->warn('Failed: ' . $email . ' (' . $response->status() . ')');
                    continue;
                }

                $result = $response->json();
                if (!empty($result['created'])) {
                    $created++;
                } elseif (!empty($result['updated'])) {
                    $updated++;
                } elseif (!empty($result['restored'])) {
                    $restored++;
                } else {
                    $unchanged++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->warn('Failed: ' . $email . ' (' . $e->getMessage() . ')');
            }
        }

        $this->line("Created: {$created}; updated: {$updated}; restored: {$restored}; unchanged: {$unchanged}; failed: {$failed}");

        return $failed > 0 ? 1 : 0;
    }
}
