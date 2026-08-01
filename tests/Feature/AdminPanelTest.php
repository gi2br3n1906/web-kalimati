<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }
}
