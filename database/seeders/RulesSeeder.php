<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rule;

class RulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rule::create([
            'action' => 'submit_form',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'staff'],
                ['field' => 'email_verified_at', 'operator' => '!=', 'value' => null],  
            ],
        ]);
    }
}
