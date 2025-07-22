<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Role;

class FixUserDoctorSeeder extends Seeder
{
    public function run()
    {
        $doctorRole = Role::where('name', 'doctor')->first();
        if (!$doctorRole) return;
        $users = User::where('role_id', $doctorRole->id)->get();
        foreach ($users as $user) {
            if (!$user->doctor) {
                Doctor::create([
                    'user_id' => $user->id,
                    'specialty' => 'Généraliste',
                    'phone' => '',
                    'bio' => '',
                ]);
            }
        }
    }
} 