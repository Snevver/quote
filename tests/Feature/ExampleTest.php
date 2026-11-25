<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/')->assertRedirect('/inchecken');
        $this->getJson('/api/quotes')->assertUnauthorized();
    }

    public function test_login_page_loads(): void
    {
        $this->get('/inchecken')->assertOk()->assertSee('data-page="Login"', false);
    }

    public function test_quotes_load_for_logged_in_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertOk()->assertSee('data-page="Quotes"', false);
        $this->actingAs($user)->getJson('/api/quotes')->assertOk()->assertJsonStructure(['quotes', 'folders']);
    }

    public function test_folder_reorder_route_is_not_swallowed_by_folder_binding(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/quote-folders/reorder', [])
            ->assertStatus(422);
    }

    public function test_session_cookie_is_app_specific_and_host_only(): void
    {
        $this->assertSame('quote_session', config('session.cookie'));
        $this->assertNull(config('session.domain'));
    }
}
