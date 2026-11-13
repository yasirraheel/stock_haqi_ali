<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountSyncController extends Controller
{
    public function upsert(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !$this->validAccountData($data)) {
            return response()->json(['ok' => false, 'message' => 'Invalid account data'], 422);
        }

        return response()->json(['ok' => true] + $this->upsertAccount($data));
    }

    public function upsertBatch(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $accounts = is_array($data) ? ($data['accounts'] ?? null) : null;
        if (!is_array($accounts) || count($accounts) > 250) {
            return response()->json(['ok' => false, 'message' => 'Invalid account batch'], 422);
        }

        $summary = ['created' => 0, 'updated' => 0, 'restored' => 0, 'unchanged' => 0, 'invalid' => 0];
        foreach ($accounts as $account) {
            if (!is_array($account) || !$this->validAccountData($account)) {
                $summary['invalid']++;
                continue;
            }

            $result = $this->upsertAccount($account);
            if ($result['created']) {
                $summary['created']++;
            } elseif ($result['updated']) {
                $summary['updated']++;
            } elseif ($result['restored']) {
                $summary['restored']++;
            } else {
                $summary['unchanged']++;
            }
        }

        return response()->json(['ok' => true, 'processed' => count($accounts)] + $summary);
    }

    private function authorized(Request $request): bool
    {
        $rawBody = $request->getContent();
        $secret = (string) config('services.account_sync.secret');
        $signature = (string) $request->header('X-Account-Sync-Signature');
        $expected = $secret !== '' ? hash_hmac('sha256', $rawBody, $secret) : '';

        return $secret !== '' && $signature !== '' && hash_equals($expected, $signature);
    }

    private function validAccountData(array $data): bool
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'password_hash' => ['required', 'string', 'max:255'],
            'reconcile' => ['sometimes', 'boolean'],
            'force_password' => ['sometimes', 'boolean'],
            'restore_deleted' => ['sometimes', 'boolean'],
        ]);

        return !$validator->fails()
            && preg_match('/^\$(2y|2b|argon2i|argon2id)\$/', (string) ($data['password_hash'] ?? ''));
    }

    private function upsertAccount(array $data): array
    {
        $email = strtolower(trim($data['email']));
        $user = User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->first();
        $created = !$user;
        $reconcile = filter_var($data['reconcile'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $forcePassword = $reconcile && filter_var($data['force_password'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $restoreDeleted = $reconcile && filter_var($data['restore_deleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$created && !$reconcile) {
            return ['created' => false, 'updated' => false, 'restored' => false, 'user_id' => $user->id];
        }

        $updated = false;
        $restored = false;

        User::withoutEvents(function () use (&$user, &$updated, &$restored, $created, $email, $data, $forcePassword, $restoreDeleted): void {
            if (!$created) {
                if ($user->trashed() && $restoreDeleted) {
                    $user->restore();
                    $restored = true;
                }

                if ($forcePassword && $user->getAuthPassword() !== $data['password_hash']) {
                    $user->password = $data['password_hash'];
                    $updated = true;
                }

                if (trim((string) $user->name) === '' && trim((string) ($data['name'] ?? '')) !== '') {
                    $user->name = trim((string) $data['name']);
                    $updated = true;
                }

                if ($updated) {
                    $user->save();
                }

                return;
            }

            $user = new User();
            $user->usertype = 'User';
            $user->name = trim((string) ($data['name'] ?? explode('@', $email)[0]));
            $user->email = $email;
            $user->password = $data['password_hash'];
            $user->save();
        });

        return ['created' => $created, 'updated' => $updated, 'restored' => $restored, 'user_id' => $user->id];
    }
}
