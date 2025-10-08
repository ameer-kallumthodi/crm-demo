<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GmvssCopyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_gmvss_copy_link_button_appears_in_leads_listing()
    {
        // Create GMVSS course
        $course = Course::create([
            'id' => 16,
            'title' => 'GMVSS',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a lead with GMVSS course
        $lead = Lead::factory()->create([
            'course_id' => 16,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that the leads listing shows the copy link button for GMVSS leads
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('copy-link-btn');
        $response->assertSee('data-url="' . route('public.lead.gmvss.register', $lead->id) . '"');
        $response->assertSee('Copy GMVSS Registration Link');
    }

    public function test_gmvss_copy_link_has_correct_url()
    {
        // Create GMVSS course
        $course = Course::create([
            'id' => 16,
            'title' => 'GMVSS',
            'amount' => 50000,
            'is_active' => true
        ]);

        // Create a lead with GMVSS course
        $lead = Lead::factory()->create([
            'course_id' => 16,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that the copy link button has the correct URL
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertSee('data-url="' . route('public.lead.gmvss.register', $lead->id) . '"');
    }

    public function test_non_gmvss_leads_dont_have_copy_link_button()
    {
        // Create non-GMVSS course
        $course = Course::create([
            'id' => 1,
            'title' => 'NIOS',
            'amount' => 30000,
            'is_active' => true
        ]);

        // Create a lead with non-GMVSS course
        $lead = Lead::factory()->create([
            'course_id' => 1,
            'title' => 'Test Student',
            'email' => 'test@example.com',
            'phone' => '1234567890'
        ]);

        // Test that non-GMVSS leads don't have the copy link button
        $response = $this->get(route('leads.index'));

        $response->assertStatus(200);
        $response->assertDontSee('copy-link-btn');
    }

    public function test_gmvss_registration_route_exists()
    {
        // Test that the GMVSS registration route exists and is accessible
        $response = $this->get(route('public.lead.gmvss.register'));

        $response->assertStatus(200);
        $response->assertSee('GMVSS Course Registration');
    }
}
