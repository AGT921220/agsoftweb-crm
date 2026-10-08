<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Features\PurchaseOrders\Application\PurchaseOrderRules;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('purchase_order');

        return $order instanceof PurchaseOrder && $this->user()->can('update', $order);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return PurchaseOrderRules::fields();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return PurchaseOrderRules::attributes();
    }
}
