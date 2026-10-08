<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::PurchaseOrdersView);
    }

    public function view(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission(Permission::PurchaseOrdersView);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::PurchaseOrdersCreate);
    }

    public function update(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission(Permission::PurchaseOrdersUpdate);
    }

    public function deliver(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission(Permission::PurchaseOrdersUpdate);
    }
}
