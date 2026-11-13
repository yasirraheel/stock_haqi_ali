<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountSyncService
{
    public function sync(User $user): void
    {
        if (!config('services.account_sync.enabled')) {
            return;
        }

        $partnerUrl = rtrim((string) config('services.account_sync.partner_url'), '/');
        $secret = (string) config('services.account_sync.secret');
        $email = strtolower(trim((string) $user->email));

        if ($partnerUrl === '' || $secret === '' || $email === '') {
            return;
        }

        $payload = [
            'email' => $email,
            'name' => (string) ($user->name ?? ''),
            'password_hash' => (string) $user->getAuthPassword(),
            'reconcile' => true,
            'force_password' => true,
            'restore_deleted' => true,
        ];
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $body, $secret);

        try {
            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Account-Sync-Signature' => $signature,
                ])
                ->withBody($body, 'application/json')
                ->post($partnerUrl . '/api/internal/account-sync');

            if (!$response->successful()) {
                Log::warning('Account sync partner rejected request', [
                    'email' => $email,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Account sync partner unavailable', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
