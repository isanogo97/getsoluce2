<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminAccessTest extends TestCase
{
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_employee_cannot_access_admin_dashboard(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/admin');

        $response->assertStatus(403);
    }
}
