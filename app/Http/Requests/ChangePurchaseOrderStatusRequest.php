<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Permission;
use App\Enums\PurchaseOrderStatus;
use App\Features\PurchaseOrders\Application\PurchaseOrderRules;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Http\FormRequest;

class ChangePurchaseOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('purchase_order');

        if (! $order instanceof PurchaseOrder) {
            return false;
        }

        $status = PurchaseOrderStatus::tryFrom((string) $this->input('status'));
        $user = $this->user();

        if ($status === PurchaseOrderStatus::Cancelled) {
            return $user->hasPermission(Permission::PurchaseOrdersCancel);
        }

        if (in_array($status, [
            PurchaseOrderStatus::Confirmed,
            PurchaseOrderStatus::InProgress,
            PurchaseOrderStatus::PartiallyDelivered,
            PurchaseOrderStatus::Completed,
        ], true)) {
            return $user->hasPermission(Permission::PurchaseOrdersConfirm);
        }

        return $user->hasPermission(Permission::PurchaseOrdersUpdate);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return PurchaseOrderRules::status();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'estado',
            'comment' => 'motivo',
        ];
    }
}
