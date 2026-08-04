<?php

declare(strict_types=1);

namespace Tests\Feature;

use Filament\Facades\Filament;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    public function test_admin_login_page_is_available(): void
    {
        $this->get('/admin/login')
            ->assertOk();
    }

    public function test_admin_panel_enables_sidebar_collapse_and_unsaved_change_alerts(): void
    {
        $panel = Filament::getPanel('admin');

        self::assertTrue($panel->isSidebarCollapsibleOnDesktop());
        self::assertTrue($panel->hasUnsavedChangesAlerts());
    }
}
