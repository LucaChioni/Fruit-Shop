<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LegalPageTest extends TestCase
{
    public function test_privacy_page_can_be_rendered(): void
    {
        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Legal/Privacy'));
    }

    public function test_cookie_policy_page_can_be_rendered(): void
    {
        $this->get(route('legal.cookies'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Legal/Cookies'));
    }

    public function test_terms_page_can_be_rendered(): void
    {
        $this->get(route('legal.terms'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Legal/Terms'));
    }
}
