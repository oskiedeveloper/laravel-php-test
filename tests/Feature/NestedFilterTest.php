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

    /**
     *  Test Case:
     *  Check if the Filters generates the correct SQL query for nested conditions.
     *  It should return a query that matches with select appointments.
     *  It should return a query containing a relationship patient.
     *  It should generate the correct SQL query with nested conditions.
     */
    public function test_generates_correct_sql_for_nested_filter()
    {
        // Assign: Define filters to test
        $filters = [
            'patient.name' => 'Oskie Villarin',
            'title' => 'Appointment test',
            'status' => 'confirmed'
        ];

        // Action: Apply the nested filter to the Appointment model
        $query = (new NestedFilter)->apply(Appointment::query(), $filters);
        $sql = $query->toSql();

        // Assert: Check if the Filters generates the correct SQL query for nested conditions.
        $this->assertStringContainsString("select * from `appointments`", $sql);
        $this->assertStringContainsString('exists (select * from `patients`', $sql); // assumes relationship
        $this->assertStringContainsString("`name` = ?", $sql);
        $this->assertStringContainsString("`title` = ?", $sql);
        $this->assertStringContainsString("`status` = ?", $sql);
    }

    /**
     *  Test Case:
     *  Test again if the Filters generates the correct SQL query for nested conditions.
     *  It should generate the correct SQL query with nested conditions.
     */
    public function test_query_generates_correct_sql_for_nested_filter()
    {
        // Assign: Create a test appointment
        $appointment = Appointment::factory()->create([
            'title' => 'Appointment test',
            'status' => 'confirmed'
        ]);
        $appointment->patient()->associate($this->patient);
        $appointment->save();

        // Assign: Define filters to test
        $filters = [
            'user.name' => 'John Doe',
            'patient.name' => 'Patient 3',
            'status' => 'cancelled',
            'location' => 'Dallas',
        ];

        // Action: Apply the nested filter to the Appointment model
        $query = (new NestedFilter)->apply(Appointment::query(), $filters);
        $sql = $query->toSql();

        // Assert: Check if the Filters generates the correct SQL query for nested conditions.
        $this->assertStringContainsString("select * from `appointments`", $sql);
        $this->assertStringContainsString('exists (select * from `patients`', $sql);
        $this->assertStringContainsString("`name` = ?", $sql);
        $this->assertStringContainsString("`status` = ?", $sql);
        $this->assertStringContainsString("`location` = ?", $sql);
    }

    /**
     *  Test Case:
     *  Check if the Filters applies the nested conditions correctly for patient name.
     *  It should return appointments that match the nested conditions for patient name.
     */
    public function test_applies_nested_conditions_for_patient_name_correctly()
    {
        // Assign: Create a test appointment
        $appointment = Appointment::factory()->create([
            'title' => 'Appointment test',
            'status' => 'confirmed'
        ]);
        $appointment->patient()->associate($this->patient);
        $appointment->save();

        // Assign: Define filters to test
        $filters = [
            'patient.name' => 'Oskie Villarin',
            'title' => 'Appointment test',
            'status' => 'confirmed'
        ];

        // Action: Apply the nested filter to the Appointment model
        $appointments = (new NestedFilter)->apply(Appointment::query(), $filters)->get();

        // Assert: Check if the Filters applies the nested conditions correctly.
        $this->assertCount(1, $appointments);
        $this->assertEquals($appointment->id, $appointments->first()->id);
    }

    /**
     *  Test Case:
     *  Check if the Filters returns an empty collection when no appointments match the conditions.
     *  It should return an empty collection when no appointments match the nested conditions.
     */
    public function test_returns_empty_collection_when_no_appointments_match_conditions()
    {
        // Assign: Define filters that do not match any appointment
        $filters = [
            'patient.name' => 'Nonexistent Patient',
            'title' => 'Nonexistent Appointment',
            'status' => 'nonexistent'
        ];

        // Action: Apply the nested filter to the Appointment model
        $appointments = (new NestedFilter)->apply(Appointment::query(), $filters)->get();

        // Assert: Check if the Filters returns an empty collection when no appointments match the conditions.
        $this->assertCount(0, $appointments);
    }

    /**
     *  Test Case:
     *  Check if the Filters handles empty filters gracefully.
     *  It should return all appointments when no filters are applied.
     */
    public function test_handles_empty_filters_gracefully()
    {
        // Assign: Create multiple appointments
        Appointment::factory()->count(3)->create();

        // Action: Apply the nested filter with empty filters
        $appointments = (new NestedFilter)->apply(Appointment::query(), [])->get();

        // Assert: Check if the Filters returns all appointments when no filters are applied.
        $this->assertCount(4, $appointments); // Note: 3 created + 1 from setUp
    }
    
}
