<?php

declare(strict_types=1);

namespace App\Features\Quotations\Application;

use App\Enums\Currency;
use App\Enums\DiscountType;
use App\Enums\FollowUpType;
use App\Enums\QuotationStatus;
use Illuminate\Validation\Rule;

final class QuotationRules
{
    /**
     * @return array<string, mixed>
     */
    public static function fields(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'issued_on' => ['required', 'date'],
            'expires_on' => ['required', 'date', 'after_or_equal:issued_on'],
            'user_id' => ['required', 'exists:users,id'],
            'currency' => ['required', Rule::enum(Currency::class)],
            'commercial_terms' => ['nullable', 'string', 'max:5000'],
            'payment_terms' => ['nullable', 'string', 'max:5000'],
            'delivery_time' => ['nullable', 'string', 'max:255'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'client_notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['nullable', 'string', 'max:80'],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['required', 'string', 'max:40'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0'],
            'items.*.discount_type' => ['required', Rule::enum(DiscountType::class)],
            'items.*.discount_value' => ['nullable', 'numeric', 'gte:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'gte:0', 'lte:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'client_name' => 'cliente',
            'legal_name' => 'razón social',
            'contact_name' => 'contacto',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'issued_on' => 'fecha de creación',
            'expires_on' => 'fecha de vencimiento',
            'user_id' => 'responsable',
            'currency' => 'moneda',
            'commercial_terms' => 'condiciones comerciales',
            'payment_terms' => 'condiciones de pago',
            'delivery_time' => 'tiempo de entrega',
            'internal_notes' => 'observaciones internas',
            'client_notes' => 'notas para el cliente',
            'items' => 'conceptos',
            'items.*.description' => 'descripción',
            'items.*.quantity' => 'cantidad',
            'items.*.unit' => 'unidad',
            'items.*.unit_price' => 'precio unitario',
            'items.*.discount_type' => 'tipo de descuento',
            'items.*.discount_value' => 'descuento',
            'items.*.tax_rate' => 'impuesto',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function status(): array
    {
        return [
            'status' => ['required', Rule::enum(QuotationStatus::class)],
            'comment' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(fn () => in_array(request()->input('status'), [
                    QuotationStatus::Rejected->value,
                    QuotationStatus::Cancelled->value,
                ], true)),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function followUp(): array
    {
        return [
            'type' => ['required', Rule::enum(FollowUpType::class)],
            'occurred_at' => ['required', 'date'],
            'result' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
