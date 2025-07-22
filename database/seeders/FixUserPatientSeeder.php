<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Role;

class FixUserPatientSeeder extends Seeder
{
    public function run()
    {
        $patientRole = Role::where('name', 'patient')->first();
        if (!$patientRole) return;
        $users = User::where('role_id', $patientRole->id)->get();
        foreach ($users as $user) {
            if (!$user->patient) {
                Patient::create([
                    'user_id' => $user->id,
                    'firstname' => $user->firstname ?? 'Patient',
                    'lastname' => $user->lastname ?? 'Inconnu',
                    'email' => $user->email,
                ]);
            }
        }
    }
} 