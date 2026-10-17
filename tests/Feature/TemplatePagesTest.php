<?php

namespace Tests\Feature;

use Tests\TestCase;

class TemplatePagesTest extends TestCase
{
    public function test_dashboard_and_authentication_pages_render_with_the_original_stylesheet(): void
    {
        foreach ([
            '/',
            '/login',
            '/register',
        ] as $path) {
            $response = $this->get($path);
            $response->assertOk()
                ->assertSee('assets/css/styles.min.css')
                ->assertSee('assets/css/bmt-theme.css')
                ->assertSee('assets/images/logos/bmt-cinde-barokah.png')
                ->assertSee('BMT CINDE BAROKAH');

            $response->assertSee($path === '/' ? 'Dashboard - BMT Cinde Barokah' : 'BMT Cinde Barokah');
        }
    }

    public function test_unused_template_demo_pages_are_removed(): void
    {
        foreach ([
            '/ui-buttons',
            '/ui-alerts',
            '/ui-card',
            '/ui-forms',
            '/ui-typography',
            '/icon-tabler',
            '/sample-page',
        ] as $path) {
            $this->get($path)->assertNotFound();
        }
    }
}
