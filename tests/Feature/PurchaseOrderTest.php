<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\Role;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesCrmUsers;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use CreatesCrmUsers;
    use RefreshDatabase;

    public function test_registers_partial_and_complete_deliveries_without_exceeding_quantity(): void
    {
        $operations = $this->userWithRole(Role::Operations);
        $this->actingAs($operations)->post(route('purchase-orders.store'), $this->orderPayload($operations));
        $order = PurchaseOrder::query()->first();
        $item = $order->items()->first();

        $this->actingAs($operations)->post(route('purchase-orders.status', $order), [
            'status' => PurchaseOrderStatus::PendingConfirmation->value,
        ])->assertRedirect();
        $this->actingAs($operations)->post(route('purchase-orders.status', $order), [
            'status' => PurchaseOrderStatus::Confirmed->value,
        ])->assertRedirect();

        $this->actingAs($operations)->post(route('purchase-orders.deliveries.store', $order), [
            'delivered_on' => '2026-10-08',
            'items' => [[
                'purchase_order_item_id' => $item->id,
                'quantity' => '4',
            ]],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(PurchaseOrderStatus::PartiallyDelivered, $order->fresh()->status);
        $this->assertSame('4.0000', $item->fresh()->quantity_delivered);

        $this->actingAs($operations)
            ->from(route('purchase-orders.show', $order))
            ->post(route('purchase-orders.deliveries.store', $order), [
                'delivered_on' => '2026-10-08',
                'items' => [[
                    'purchase_order_item_id' => $item->id,
                    'quantity' => '7',
                ]],
            ])
            ->assertSessionHas('error');
        $this->assertSame('4.0000', $item->fresh()->quantity_delivered);

        $this->actingAs($operations)->post(route('purchase-orders.deliveries.store', $order), [
            'delivered_on' => '2026-10-09',
            'items' => [[
                'purchase_order_item_id' => $item->id,
                'quantity' => '6',
            ]],
        ])->assertSessionHas('success');

        $this->assertSame(PurchaseOrderStatus::Completed, $order->fresh()->status);
        $this->assertSame('10.0000', PurchaseOrderItem::query()->find($item->id)->quantity_delivered);
    }

    public function test_cancelled_orders_do_not_accept_deliveries(): void
    {
        $operations = $this->userWithRole(Role::Operations);
        $this->actingAs($operations)->post(route('purchase-orders.store'), $this->orderPayload($operations));
        $order = PurchaseOrder::query()->first();

        $this->actingAs($operations)->post(route('purchase-orders.status', $order), [
            'status' => PurchaseOrderStatus::Cancelled->value,
            'comment' => 'El cliente canceló',
        ])->assertSessionHas('success');

        $this->actingAs($operations)
            ->from(route('purchase-orders.show', $order))
            ->post(route('purchase-orders.deliveries.store', $order), [
                'delivered_on' => '2026-10-08',
                'items' => [[
                    'purchase_order_item_id' => $order->items()->first()->id,
                    'quantity' => '1',
                ]],
            ])
            ->assertSessionHas('error');

        $this->assertSame('0.0000', $order->items()->first()->quantity_delivered);
    }
}
