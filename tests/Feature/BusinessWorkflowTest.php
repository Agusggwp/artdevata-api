<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BusinessWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $superAdmin;
    protected Admin $staffAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\SecuritySeeder::class);

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $this->superAdmin = Admin::create([
            'name' => 'Super Test Admin',
            'email' => 'supertest@artdevata.com',
            'password' => Hash::make('password123'),
            'role_id' => $superAdminRole->id,
            'status' => 'active',
        ]);

        $staffRole = Role::where('slug', 'staff')->first();
        $this->staffAdmin = Admin::create([
            'name' => 'Staff Test Admin',
            'email' => 'stafftest@artdevata.com',
            'password' => Hash::make('password123'),
            'role_id' => $staffRole->id,
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_create_lead(): void
    {
        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('admin.leads.store'), [
                'name' => 'PT IndoZone Solusi',
                'company_name' => 'PT IndoZone Solusi',
                'email' => 'contact@indozone.id',
                'phone' => '08123456789',
                'source' => 'website',
                'service_interest' => 'Website Development',
                'estimated_budget' => 15000000,
                'status' => 'new',
            ]);

        $response->assertRedirect(route('admin.leads.index'));
        $this->assertDatabaseHas('leads', [
            'name' => 'PT IndoZone Solusi',
            'email' => 'contact@indozone.id',
            'status' => 'new',
        ]);
    }

    public function test_lead_can_be_converted_to_client(): void
    {
        $lead = Lead::create([
            'name' => 'CV Bali Media',
            'company_name' => 'CV Bali Media',
            'email' => 'info@balimedia.com',
            'phone' => '08987654321',
            'source' => 'whatsapp',
            'service_interest' => 'Network Setup',
            'estimated_budget' => 8000000,
            'status' => 'negotiation',
        ]);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('admin.leads.convert', $lead->id));

        $lead->refresh();
        $this->assertNotNull($lead->client_id);
        $this->assertEquals('won', $lead->status);

        $response->assertRedirect(route('admin.clients.show', $lead->client_id));
        $this->assertDatabaseHas('clients', [
            'id' => $lead->client_id,
            'email' => 'info@balimedia.com',
        ]);
    }

    public function test_quotation_creation_calculates_totals_securely_on_backend(): void
    {
        $client = Client::create([
            'name' => 'PT Megah Utama',
            'company_name' => 'PT Megah Utama',
            'email' => 'procurement@megah.co.id',
            'status' => 'active',
        ]);

        $service = Service::create([
            'title' => 'CCTV Installation',
            'description' => 'Instalasi Kamera CCTV HD',
        ]);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->post(route('admin.quotations.store'), [
                'quotation_number' => 'QT-202609-001',
                'client_id' => $client->id,
                'issue_date' => date('Y-m-d'),
                'valid_until' => date('Y-m-d', strtotime('+14 days')),
                'status' => 'draft',
                'discount' => 500000,
                'tax' => 1000000,
                'items' => [
                    [
                        'service_id' => $service->id,
                        'description' => 'Paket CCTV 8 Kamera HD',
                        'quantity' => 2,
                        'unit' => 'paket',
                        'unit_price' => 5000000, // Subtotal item 10.000.000
                        'discount' => 0,
                    ]
                ]
            ]);

        $quotation = Quotation::where('quotation_number', 'QT-202609-001')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(10000000, $quotation->subtotal);
        $this->assertEquals(500000, $quotation->discount);
        $this->assertEquals(1000000, $quotation->tax);
        $this->assertEquals(10500000, $quotation->total); // (10m - 500k) + 1m = 10.5m
    }

    public function test_accepted_quotation_can_be_converted_to_project(): void
    {
        $client = Client::create([
            'name' => 'Surya Technic',
            'company_name' => 'Surya Technic',
            'status' => 'active',
        ]);

        $quotation = Quotation::create([
            'quotation_number' => 'QT-202609-002',
            'client_id' => $client->id,
            'issue_date' => date('Y-m-d'),
            'valid_until' => date('Y-m-d', strtotime('+14 days')),
            'status' => 'accepted',
            'subtotal' => 5000000,
            'total' => 5000000,
        ]);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('admin.quotations.create-project', $quotation->id));

        $quotation->refresh();
        $this->assertNotNull($quotation->project_id);

        $project = Project::find($quotation->project_id);
        $this->assertNotNull($project);
        $this->assertEquals($client->id, $project->client_id);
        $this->assertEquals(5000000, $project->budget);
        $this->assertEquals('planning', $project->status);
    }

    public function test_project_tasks_auto_recalculates_progress(): void
    {
        $project = Project::create([
            'project_number' => 'PRJ-202609-001',
            'name' => 'Network Installation',
            'status' => 'in_progress',
            'priority' => 'high',
            'budget' => 12000000,
            'progress' => 0,
            'admin_id' => $this->superAdmin->id,
        ]);

        // Add task 1
        $this->actingAs($this->superAdmin, 'admin')
            ->post(route('admin.projects.tasks.store', $project->id), [
                'title' => 'Pull Fiber Cable',
                'priority' => 'high',
                'status' => 'done',
            ]);

        // Add task 2
        $this->actingAs($this->superAdmin, 'admin')
            ->post(route('admin.projects.tasks.store', $project->id), [
                'title' => 'Configure Router',
                'priority' => 'normal',
                'status' => 'todo',
            ]);

        $project->refresh();
        // 1 done out of 2 tasks => 50%
        $this->assertEquals(50, $project->progress);
    }

    public function test_global_search_returns_json_results(): void
    {
        Lead::create([
            'name' => 'IndoZone Global',
            'source' => 'website',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->superAdmin, 'admin')
            ->get(route('admin.global-search', ['q' => 'IndoZone']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'IndoZone Global']);
    }

    public function test_unauthorized_staff_cannot_delete_leads(): void
    {
        $lead = Lead::create([
            'name' => 'Protected Prospek',
            'source' => 'website',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->staffAdmin, 'admin')
            ->delete(route('admin.leads.destroy', $lead->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('leads', ['id' => $lead->id]);
    }
}
