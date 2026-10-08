<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Features\PurchaseOrders\Application\PurchaseOrderRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('purchase_order');

        return $order && $this->user()->can('deliver', $order);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return PurchaseOrderRules::delivery();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'delivered_on' => 'fecha de entrega',
            'notes' => 'notas',
            'items' => 'cantidades',
            'items.*.quantity' => 'cantidad',
        ];
    }
}
