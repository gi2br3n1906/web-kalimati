<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class RobotsController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        return response()->file(public_path('robots.txt'), [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
