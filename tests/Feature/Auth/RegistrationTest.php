<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_route_redirects_to_login(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/login');
    }

    public function test_admin_user_is_created_by_database_seeder(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertFalse($admin->is_admin);
    }
}
