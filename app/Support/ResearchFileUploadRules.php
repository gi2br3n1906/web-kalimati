<?php

declare(strict_types=1);

namespace App\Support;

final class ResearchFileUploadRules
{
    public const MAX_SIZE_KB = 20_480;

    /** @var array<int, string> */
    public const MIME_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /** @return array<int, string> */
    public static function rules(): array
    {
        return ['required', 'file', 'mimetypes:'.implode(',', self::MIME_TYPES), 'max:'.self::MAX_SIZE_KB];
    }
}
