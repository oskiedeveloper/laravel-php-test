<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Database\Seeders\PatientAppointmentSeeder;

use App\Utils\Filters\NestedFilter;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;

class NestedFilterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Patient $patient;
    protected Appointment $appointment;

    /**
     *  Set up the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Initialize a test user
        $this->user = User::factory()->create();

        // Initialize a patient
        $this->patient = Patient::factory()->create([
            'name' => 'Oskie Villarin'
        ]);

        // Initialize an appointment
        $this->appointment = Appointment::factory()->create();
    }

    /**
     *  Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Clean up the test user and rules
        User::truncate();
        Patient::truncate();
        Appointment::truncate();
        parent::tearDown();
    }

    public function test_generates_correct_sql_for_nested_filter()
    {
        // test filters
        $filters = [
            'patient.name' => 'Oskie Villarin',
            'title' => 'Appointment test',
            'status' => 'confirmed'
        ];

        // Query with Filters
        $query = (new NestedFilter)->apply(Appointment::query(), $filters);
        $sql = $query->toSql();

        $this->assertStringContainsString("select * from `appointments`", $sql);
        $this->assertStringContainsString('exists (select * from `patients`', $sql); // assumes relationship
        $this->assertStringContainsString("`name` = ?", $sql);
        $this->assertStringContainsString("`title` = ?", $sql);
        $this->assertStringContainsString("`status` = ?", $sql);
    }
}
