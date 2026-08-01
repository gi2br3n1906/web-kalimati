<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleType: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN_DESA = 'admin_desa';
    case KELOMPOK_TANI = 'kelompok_tani';
    case UMKM = 'umkm';
    case WARGA = 'warga';

    /**
     * @return array<string>
     */
    public static function backofficeRoles(): array
    {
        return [
            self::SUPER_ADMIN->value,
            self::ADMIN_DESA->value,
            self::KELOMPOK_TANI->value,
            self::UMKM->value,
        ];
    }
}
