<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function selectRole()
    {
        $roles = Role::all();
        return view('auth.select-role', compact('roles'));
    }

    public function showRegisterForm($role)
    {
        if ($role === 'doctor') {
            return view('auth.register_doctor');
        } elseif ($role === 'pharmacist') {
            return view('auth.register_pharmacist');
        } elseif ($role === 'patient') {
            return view('auth.register_patient');
        } else {
            abort(404);
        }
    }

    public function register(Request $request, $role)
    {
        // Validation dynamique selon le rôle
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
        if ($role === 'doctor') {
            $rules['specialty'] = 'required|string|max:255';
            $rules['phone'] = 'required|string|max:255';
            // bio reste facultatif
        }
        $request->validate($rules);

        // Récupérer le rôle
        $roleModel = Role::where('name', $role)->firstOrFail();

        // Générer le champ name automatiquement
        $name = $request->has(['firstname', 'lastname'])
            ? trim($request->firstname . ' ' . $request->lastname)
            : ($request->name ?? '');

        // Créer l'utilisateur
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'name' => $name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $roleModel->id,
        ]);

        // Si c'est un médecin, créer l'entrée dans la table doctors
        if ($role === 'doctor') {
            \Log::info('Données reçues pour inscription médecin : ' . json_encode($request->all()));
            \App\Models\Doctor::create([
                'user_id' => $user->id,
                'specialty' => $request->specialty,
                'bio' => $request->bio ?? null,
                'phone' => $request->phone,
            ]);
        }
        // Si c'est un patient, créer l'entrée dans la table patients
        if ($role === 'patient') {
            \App\Models\Patient::create([
                'user_id' => $user->id,
                'phone' => $request->phone ?? null,
                'birthdate' => $request->birth_date ?? null,
                'gender' => $request->gender ?? null,
                // Ajoute ici d'autres champs spécifiques si besoin
            ]);
        }
        // Si c'est un pharmacien, créer l'entrée dans la table pharmacists
        if ($role === 'pharmacist') {
            \App\Models\Pharmacist::create([
                'user_id' => $user->id,
                // Ajoute ici d'autres champs spécifiques si besoin
            ]);
        }

        // Connexion automatique
        Auth::guard('web')->login($user);

        // Redirection selon le rôle
        if ($role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($role === 'pharmacist') {
            return redirect()->route('pharmacist.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('web')->user();
            if ($user->role && $user->role->name === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role && $user->role->name === 'doctor') {
                return redirect()->route('doctor.dashboard');
            } elseif ($user->role && $user->role->name === 'pharmacist') {
                return redirect()->route('pharmacist.dashboard');
            } else {
                return redirect()->route('patient.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ]);
    }

    public function editProfile()
    {
        // Logique d'édition de profil
    }

    public function updateProfile(Request $request)
    {
        // Logique de mise à jour de profil
    }

    public function redirectToGoogle()
    {
        // Logique de redirection Google OAuth
    }

    public function handleGoogleCallback()
    {
        // Logique de callback Google OAuth
    }
} 