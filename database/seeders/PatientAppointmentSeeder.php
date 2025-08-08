<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Patient;
use App\Models\Appointment;
use Carbon\Carbon;

class PatientAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = ['Dallas', 'Austin', 'Houston'];
        $statuses = ['confirmed', 'cancelled', 'pending'];

        foreach (range(1, 10) as $i) {
            $patient = Patient::create([
                'name' => 'Patient ' . $i,
                'email' => 'patient' . $i . '@example.com',
                'phone' => '555-000' . $i,
            ]);

            foreach (range(1, rand(1, 3)) as $j) {
                Appointment::create([
                    'user_id' => 1, // Assuming a default user ID for appointments
                    'patient_id' => $patient->id,
                    'title' => 'Appointment ' . $j . ' for Patient ' . $i,
                    'description' => 'Description for appointment ' . $j,
                    'location' => $cities[array_rand($cities)],
                    'date' => Carbon::now()->addDays(rand(1, 30)),
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => 'Notes for appointment ' . $j,
                ]);
            }
        }
    }
}
