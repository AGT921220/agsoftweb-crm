<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Features\PurchaseOrders\Application\PurchaseOrderRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\PurchaseOrder::class);
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
