<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_can_be_changed_to_english(): void
    {
        $this->post(route('language.update', 'en'))
            ->assertRedirect();

        $this
            ->withSession(['locale' => 'en'])
            ->get(route('products.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('locale', 'en')
                ->where('translations', fn ($translations) => $translations->get('nav.products') === 'Products'));
    }

    public function test_invalid_language_is_rejected(): void
    {
        $this->post(route('language.update', 'fr'))
            ->assertNotFound();
    }

    public function test_authenticated_user_language_is_saved_for_future_emails(): void
    {
        $user = User::factory()->create(['locale' => 'it']);

        $this->actingAs($user)
            ->post(route('language.update', 'en'))
            ->assertRedirect();

        $this->assertSame('en', $user->refresh()->locale);
    }
}
