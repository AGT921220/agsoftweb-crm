<?php

declare(strict_types=1);

namespace App\Features\PurchaseOrders\Application;

use App\Enums\ActivityAction;
use App\Enums\PurchaseOrderStatus;
use App\Exceptions\CrmRuleException;
use App\Features\Shared\Application\RecordActivity;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

final class ChangePurchaseOrderStatus
{
    public function __construct(private readonly RecordActivity $activity) {}

    public function __invoke(PurchaseOrder $order, PurchaseOrderStatus $status, User $user, ?string $comment = null): PurchaseOrder
    {
        if (! in_array($status, $order->status->transitions(), true)) {
            throw new CrmRuleException('No se puede cambiar de '.$order->status->label().' a '.$status->label().'.');
        }

        if ($status === PurchaseOrderStatus::Cancelled && blank($comment)) {
            throw new CrmRuleException('El motivo es obligatorio para cancelar la orden.');
        }

        if ($status === PurchaseOrderStatus::Completed) {
            $order->load('items');
            $pending = $order->items->contains(
                fn ($item) => Money::gt((string) $item->quantity, (string) $item->quantity_delivered)
            );

            if ($pending) {
                throw new CrmRuleException('No se puede completar la orden mientras existan cantidades pendientes.');
            }
        }

        return DB::transaction(function () use ($order, $status, $user, $comment) {
            $from = $order->status;
            $order->status = $status;
            $order->save();
            ($this->activity)($order, ActivityAction::StatusChanged, $user, $comment, [
                'from' => $from->value,
                'to' => $status->value,
            ]);

            return $order;
        });
    }
}
