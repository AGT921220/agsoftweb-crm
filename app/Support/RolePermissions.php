<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Permission;
use App\Enums\Role;

final class RolePermissions
{
    /**
     * @return list<Permission>
     */
    public static function for(Role $role): array
    {
        if ($role === Role::Admin) {
            return Permission::cases();
        }

        if ($role === Role::Sales) {
            return [
                Permission::QuotationsView,
                Permission::QuotationsCreate,
                Permission::QuotationsUpdate,
                Permission::QuotationsApprove,
                Permission::QuotationsCancel,
                Permission::QuotationsHistory,
                Permission::QuotationsFollowUp,
                Permission::PurchaseOrdersView,
                Permission::PurchaseOrdersCreate,
            ];
        }

        return [
            Permission::QuotationsView,
            Permission::QuotationsHistory,
            Permission::PurchaseOrdersView,
            Permission::PurchaseOrdersCreate,
            Permission::PurchaseOrdersUpdate,
            Permission::PurchaseOrdersConfirm,
            Permission::PurchaseOrdersCancel,
        ];
    }
}
