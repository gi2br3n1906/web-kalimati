<?php

declare(strict_types=1);

namespace App\Enums;

enum PoiCategory: string
{
    case FASILITAS_UMUM_PEMERINTAHAN = 'pemerintahan';
    case UMKM_EKONOMI = 'umkm_ekonomi';
    case TEMPAT_IBADAH = 'ibadah';
    case PENDIDIKAN_KESEHATAN = 'pendidikan';
    case INFRASTRUKTUR_TRANSPORTASI = 'infrastruktur_transportasi';
    case PERTANIAN_LINGKUNGAN = 'pertanian_iot';

    public const PEMERINTAHAN = self::FASILITAS_UMUM_PEMERINTAHAN;

    public const FASILITAS_UMUM = self::FASILITAS_UMUM_PEMERINTAHAN;

    public const IBADAH = self::TEMPAT_IBADAH;

    public const PENDIDIKAN = self::PENDIDIKAN_KESEHATAN;

    public const POSYANDU = self::PENDIDIKAN_KESEHATAN;

    public const PERTANIAN_IOT = self::PERTANIAN_LINGKUNGAN;

    public function label(): string
    {
        return match ($this) {
            self::FASILITAS_UMUM_PEMERINTAHAN => 'Fasilitas Umum & Pemerintahan',
            self::UMKM_EKONOMI => 'UMKM & Ekonomi',
            self::TEMPAT_IBADAH => 'Tempat Ibadah',
            self::PENDIDIKAN_KESEHATAN => 'Pendidikan & Kesehatan',
            self::INFRASTRUKTUR_TRANSPORTASI => 'Infrastruktur & Transportasi',
            self::PERTANIAN_LINGKUNGAN => 'Pertanian & Lingkungan',
        };
    }

    public function defaultMarker(): string
    {
        return match ($this) {
            self::FASILITAS_UMUM_PEMERINTAHAN => 'building-government',
            self::UMKM_EKONOMI => 'storefront',
            self::TEMPAT_IBADAH => 'place-of-worship',
            self::PENDIDIKAN_KESEHATAN => 'education-health',
            self::INFRASTRUKTUR_TRANSPORTASI => 'transport',
            self::PERTANIAN_LINGKUNGAN => 'agriculture-environment',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        $normalizedLabel = trim($label);

        foreach (self::cases() as $category) {
            if (strcasecmp($category->label(), $normalizedLabel) === 0) {
                return $category;
            }
        }

        return null;
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
            'building-government' => 'Fasilitas Umum / Pemerintahan',
            'storefront' => 'UMKM / Ekonomi',
            'place-of-worship' => 'Tempat Ibadah',
            'education-health' => 'Pendidikan / Kesehatan',
            'transport' => 'Infrastruktur / Transportasi',
            'agriculture-environment' => 'Pertanian / Lingkungan',
        ];
    }
}
