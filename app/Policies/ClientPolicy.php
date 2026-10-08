<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::QuotationsView)
            || $user->hasPermission(Permission::PurchaseOrdersView);
    }

    public function view(User $user, Client $client): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::QuotationsCreate)
            || $user->hasPermission(Permission::PurchaseOrdersCreate);
    }

    public function update(User $user, Client $client): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermission(Permission::QuotationsUpdate)
            || $user->hasPermission(Permission::PurchaseOrdersUpdate);
    }
}
