<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Sales = 'sales';
    case Operations = 'operations';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administración',
            self::Sales => 'Ventas',
            self::Operations => 'Operaciones',
        };
    }
}
