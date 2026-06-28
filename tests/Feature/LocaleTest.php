<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LocaleTest extends TestCase
{
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
}
