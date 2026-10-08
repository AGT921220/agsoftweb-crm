<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesCrmUsers;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use CreatesCrmUsers;
    use RefreshDatabase;

    public function test_login_uses_nickname_and_the_shared_password(): void
    {
        $admin = $this->userWithRole(Role::Admin);
        $admin->update([
            'nickname' => 'admin',
            'password' => 'admin',
        ]);

        $this->post(route('login'), [
            'nickname' => 'admin',
            'password' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'admin',
        ])->assertSessionHasErrors('nickname');
    }

    public function test_operations_cannot_approve_quotations_and_sales_cannot_confirm_orders(): void
    {
        $sales = $this->userWithRole(Role::Sales);
        $operations = $this->userWithRole(Role::Operations);

        $this->actingAs($sales)->post(route('quotations.store'), $this->quotationPayload($sales));
        $quotation = Quotation::query()->first();
        $this->actingAs($sales)->post(route('quotations.status', $quotation), ['status' => 'enviada']);

        $this->actingAs($operations)
            ->post(route('quotations.status', $quotation), ['status' => 'aprobada'])
            ->assertForbidden();

        $this->actingAs($operations)
            ->post(route('quotations.store'), $this->quotationPayload($operations))
            ->assertForbidden();

        $this->actingAs($operations)->post(route('purchase-orders.store'), $this->orderPayload($operations))->assertRedirect();

        $this->actingAs($sales)
            ->post(route('purchase-orders.status', \App\Models\PurchaseOrder::query()->first()), [
                'status' => 'confirmada',
            ])
            ->assertForbidden();

        $this->actingAs($sales)->get(route('quotations.show', $quotation))->assertOk();
        $this->actingAs($operations)->get(route('dashboard'))->assertOk();
    }
}
