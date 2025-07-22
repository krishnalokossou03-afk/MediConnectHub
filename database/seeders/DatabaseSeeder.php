<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // S'assurer que le rôle 'admin' existe
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // S'assurer que le rôle 'doctor' existe
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        // S'assurer que le rôle 'patient' existe
        $patientRole = Role::firstOrCreate(['name' => 'patient']);

        // Exemple de seed pour des docteurs de différentes spécialités
        $doctorsData = [
            [
                'firstname' => 'Alice',
                'lastname' => 'Dupont',
                'email' => 'alice.dupont@example.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => json_encode(['Cardiologie', 'Médecine générale']),
                'bio' => 'Spécialiste du cœur et médecin généraliste.',
                'phone' => '0600000001',
            ],
            [
                'firstname' => 'Jean',
                'lastname' => 'Martin',
                'email' => 'jean.martin@example.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => 'Pédiatrie, Neurologie',
                'bio' => 'Expert en pédiatrie et neurologie.',
                'phone' => '0600000002',
            ],
            [
                'firstname' => 'Fatou',
                'lastname' => 'Sow',
                'email' => 'fatou.sow@example.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => 'Dermatologie',
                'bio' => 'Dermatologue expérimentée.',
                'phone' => '0600000003',
            ],
        ];

        foreach ($doctorsData as $data) {
            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'name' => $data['firstname'] . ' ' . $data['lastname'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
            ]);
            Doctor::create([
                'user_id' => $user->id,
                'specialty' => $data['specialty'],
                'bio' => $data['bio'] ?? null,
                'phone' => $data['phone'],
            ]);
        }

        // Création de médecins de test
        $doctorsData = [
            [
                'firstname' => 'Jean',
                'lastname' => 'Martin',
                'email' => 'jean.martin@medicarehub.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => 'Cardiologie',
                'phone' => '0600000001',
            ],
            [
                'firstname' => 'Sophie',
                'lastname' => 'Lambert',
                'email' => 'sophie.lambert@medicarehub.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => 'Dermatologie',
                'phone' => '0600000002',
            ],
            [
                'firstname' => 'Pierre',
                'lastname' => 'Lefèvre',
                'email' => 'pierre.lefevre@medicarehub.com',
                'password' => bcrypt('password'),
                'role_id' => $doctorRole->id,
                'specialty' => 'Ophtalmologie',
                'phone' => '0600000003',
            ],
        ];
        foreach ($doctorsData as $data) {
            $user = User::create([
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'name' => $data['firstname'] . ' ' . $data['lastname'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'],
            ]);
            Doctor::create([
                'user_id' => $user->id,
                'specialty' => $data['specialty'],
                'phone' => $data['phone'],
            ]);
        }

        // Création de l'utilisateur administrateur unique
        $adminEmail = 'lucmariolokossou@gmail.com';
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'firstname' => 'Luçmario',
                'lastname' => 'Lokossou',
                'name' => 'Luçmario Lokossou',
                'email' => $adminEmail,
                'password' => bcrypt('motdepasseadmin'), // Mot de passe admin modifié
                'role_id' => $adminRole->id,
            ]);
        } else {
            $admin = User::where('email', $adminEmail)->first();
            $admin->firstname = 'Luçmario';
            $admin->lastname = 'Lokossou';
            $admin->name = 'Luçmario Lokossou';
            $admin->role_id = $adminRole->id;
            $admin->password = bcrypt('motdepasseadmin');
            $admin->save();
        }

        // Création de l'utilisateur patient supplémentaire
        $otherPatientEmail = 'mediconnect.bj@gmail.com';
        if (!User::where('email', $otherPatientEmail)->exists()) {
            User::create([
                'firstname' => 'MediConnect',
                'lastname' => 'BJ',
                'name' => 'MediConnect BJ',
                'email' => $otherPatientEmail,
                'password' => bcrypt('motdepasse'),
                'role_id' => $patientRole->id,
            ]);
        
            $otherPatient = User::where('email', $otherPatientEmail)->first();
            $otherPatient->role_id = $patientRole->id;
            $otherPatient->password = bcrypt('motdepasse');
            $otherPatient->save();
        }

        // Synchronisation des users doctor -> doctors
        $this->call(\Database\Seeders\FixUserDoctorSeeder::class);
        // Synchronisation des users patient -> patients
        $this->call(\Database\Seeders\FixUserPatientSeeder::class);
    }
}