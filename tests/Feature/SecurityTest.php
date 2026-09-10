<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Database\Seeders\SecuritySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SecuritySeeder::class);
    }

    /**
     * Test admin login rate limiting and generic error message.
     */
    public function test_login_rate_limiting_and_generic_error()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('admin.login'), [
                'email'    => 'invalid@artdevata.com',
                'password' => 'wrongpassword',
            ]);
            $response->assertSessionHas('error', 'Email atau password tidak valid.');
        }

        $response = $this->post(route('admin.login'), [
            'email'    => 'invalid@artdevata.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Terlalu banyak percobaan login', session('error'));
    }

    /**
     * Test role permission authorization returns 403 when user lacks permission.
     */
    public function test_permission_authorization_denies_unauthorized_access()
    {
        $staffRole = Role::where('slug', 'staff')->first();

        $staffAdmin = Admin::create([
            'name'     => 'Staff Test User',
            'email'    => 'stafftest@artdevata.com',
            'password' => Hash::make('password123'),
            'role_id'  => $staffRole->id,
            'status'   => 'active',
        ]);

        // Staff does not have permission: roles.manage
        $response = $this->actingAs($staffAdmin, 'admin')
                         ->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    /**
     * Test Super Admin has full access to protected routes.
     */
    public function test_super_admin_has_full_access()
    {
        $superRole = Role::where('slug', 'super-admin')->first();

        $superAdmin = Admin::create([
            'name'     => 'Super Test Admin',
            'email'    => 'supertest@artdevata.com',
            'password' => Hash::make('password123'),
            'role_id'  => $superRole->id,
            'status'   => 'active',
        ]);

        $response = $this->actingAs($superAdmin, 'admin')
                         ->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    /**
     * Test admin cannot delete their own account.
     */
    public function test_admin_cannot_delete_self()
    {
        $superRole = Role::where('slug', 'super-admin')->first();

        $admin = Admin::create([
            'name'     => 'Self Delete Admin',
            'email'    => 'selfdelete@artdevata.com',
            'password' => Hash::make('password123'),
            'role_id'  => $superRole->id,
            'status'   => 'active',
        ]);

        $response = $this->actingAs($admin, 'admin')
                         ->delete(route('admin.users.destroy', $admin->id));

        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        $this->assertDatabaseHas('admins', ['id' => $admin->id]);
    }
}
