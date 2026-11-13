<?php

namespace App\Observers;

use App\Services\AccountSyncService;
use App\User;

class AccountSyncObserver
{
    public function created(User $user): void
    {
        app(AccountSyncService::class)->sync($user);
    }
}
