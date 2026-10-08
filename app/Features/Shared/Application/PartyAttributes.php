<?php

declare(strict_types=1);

namespace App\Features\Shared\Application;

final class PartyAttributes
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function from(array $data): array
    {
        return [
            'client_id' => ($data['client_id'] ?? null) ?: null,
            'client_name' => $data['client_name'],
            'legal_name' => $data['legal_name'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ];
    }
}
