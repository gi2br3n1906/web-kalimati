<?php

declare(strict_types=1);

namespace App\Enums;

enum PoiCategory: string
{
    case PEMERINTAHAN = 'pemerintahan';
    case FASILITAS_UMUM = 'fasilitas_umum';
    case PENDIDIKAN = 'pendidikan';
    case PERTANIAN_IOT = 'pertanian_iot';
    case IBADAH = 'ibadah';
    case POSYANDU = 'posyandu';

    public function label(): string
    {
        return match ($this) {
            self::PEMERINTAHAN => 'Pemerintahan',
            self::FASILITAS_UMUM => 'Fasilitas Umum',
            self::PENDIDIKAN => 'Pendidikan',
            self::PERTANIAN_IOT => 'Pertanian / IoT',
            self::IBADAH => 'Tempat Ibadah',
            self::POSYANDU => 'Posyandu',
        };
    }

    public function defaultMarker(): string
    {
        return match ($this) {
            self::PEMERINTAHAN => 'building-government',
            self::FASILITAS_UMUM => 'landmark',
            self::PENDIDIKAN => 'school',
            self::PERTANIAN_IOT => 'agriculture-iot',
            self::IBADAH => 'place-of-worship',
            self::POSYANDU => 'health-center',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $category) {
            $options[$category->value] = $category->label();
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function markerOptions(): array
    {
        return [
            'building-government' => 'Kantor Pemerintahan',
            'landmark' => 'Fasilitas Umum',
            'school' => 'Sekolah',
            'agriculture-iot' => 'Pertanian / IoT',
            'place-of-worship' => 'Tempat Ibadah',
            'health-center' => 'Pusat Kesehatan',
        ];
    }
}
