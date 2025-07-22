<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FedaPayController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Accueil (page d'accueil nommée 'home')
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard Admin
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/appointments', [\App\Http\Controllers\Admin\DashboardController::class, 'appointments'])->name('admin.appointments');
    Route::delete('/admin/appointments/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'destroyAppointment'])->name('admin.appointments.delete');
    Route::get('/admin/appointments/{id}/edit', [\App\Http\Controllers\Admin\DashboardController::class, 'editAppointment'])->name('admin.appointments.edit');
    Route::post('/admin/appointments/{id}/update', [\App\Http\Controllers\Admin\DashboardController::class, 'updateAppointment'])->name('admin.appointments.update');
});

// Dashboard général
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Dashboards personnalisés
Route::get('/patient/dashboard', [App\Http\Controllers\PatientController::class, 'dashboardPatient'])->name('patient.dashboard');
Route::get('/doctor/dashboard', [App\Http\Controllers\DoctorController::class, 'dashboardDoctor'])->name('doctor.dashboard');
Route::get('/pharmacist/dashboard', [App\Http\Controllers\PharmacistController::class, 'dashboardPharmacist'])->name('pharmacist.dashboard');

// Routes ressources pour chaque entité
Route::middleware('auth')->group(function () {
    // Prise de rendez-vous patient : choix spécialité
    Route::get('/appointments/request', [App\Http\Controllers\AppointmentController::class, 'requestForm'])->name('appointments.request');
    // Prise de rendez-vous : enregistrement
    Route::post('/appointments/book', [App\Http\Controllers\AppointmentController::class, 'book'])->name('appointments.book');
    Route::resource('patients', PatientController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::resource('medical-records', MedicalRecordController::class);
    Route::resource('consultations', ConsultationController::class);
    Route::resource('prescriptions', PrescriptionController::class);
    Route::resource('bills', BillController::class);
    Route::resource('pharmacists', PharmacistController::class);
    Route::resource('cashiers', CashierController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

// ------------------- AUTHENTIFICATION MULTI-ROLES -------------------
// Sélection du rôle
Route::get('/select-role', [AuthController::class, 'selectRole'])->name('select.role');

// Inscription par rôle
Route::get('/register/{role}', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register/{role}', [AuthController::class, 'register'])->name('register');

// Connexion
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Connexion Admin
Route::get('/admin/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('admin.login.submit');

// Profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Auth Google
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
// --------------------------------------------------------------------

// Autres pages statiques
Route::view('/a-propos', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

// Route de déconnexion (logout)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');
Route::post('/admin/logout', [\App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('admin.logout');

// Paiement FedaPay
Route::post('/fedapay/pay', [FedaPayController::class, 'initiatePayment'])->name('fedapay.pay');
Route::get('/fedapay/callback', [FedaPayController::class, 'callback'])->name('fedapay.callback');
Route::get('/fedapay/return', [FedaPayController::class, 'return'])->name('fedapay.return');

Route::get('/fedapay/form', function() {
    return view('fedapay_form');
});

// Réinitialisation du mot de passe
// Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
// Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Exemple carte OSM/Leaflet
Route::get('/map-example', function () {
    return view('map-example');
})->name('map.example');

// Actions sur les rendez-vous (médecin)
Route::post('/doctor/appointments/{appointment}/confirm', [App\Http\Controllers\DoctorController::class, 'confirmAppointment'])->name('doctor.appointments.confirm');
Route::post('/doctor/appointments/{appointment}/refuse', [App\Http\Controllers\DoctorController::class, 'refuseAppointment'])->name('doctor.appointments.refuse');
// Action sur les ordonnances
Route::post('/doctor/prescriptions/{prescription}/validate', [App\Http\Controllers\DoctorController::class, 'validatePrescription'])->name('doctor.prescriptions.validate');
// Historique complet
Route::get('/doctor/history', [App\Http\Controllers\DoctorController::class, 'history'])->name('doctor.history');
// Historique complet pharmacien
Route::get('/pharmacist/history', [App\Http\Controllers\PharmacistController::class, 'history'])->name('pharmacist.history');

Route::middleware(['auth'])->group(function () {
    Route::get('/teleconsultation/{room}', function($room) {
        $user = Auth::user();
        $allowedRoles = ['patient', 'doctor', 'pharmacist'];
        if (!in_array($user->role->name, $allowedRoles)) {
            abort(403, 'Accès refusé à la téléconsultation.');
        }
        return view('teleconsultation.room', compact('room'));
    })->name('teleconsultation.room');
    Route::get('/teleconsultation/appointment/{appointment}', [App\Http\Controllers\AppointmentController::class, 'teleconsultationRoom'])->name('teleconsultation.appointment');
    Route::get('/teleconsultation/chat/{appointment}', [App\Http\Controllers\AppointmentController::class, 'getTeleconsultationChat']);
    Route::post('/teleconsultation/chat/{appointment}', [App\Http\Controllers\AppointmentController::class, 'postTeleconsultationChat']);
});

// Route temporaire pour la démo de visioconférence patient/médecin
Route::get('/teleconsultation-demo', function() {
    return view('teleconsultation.demo');
})->name('teleconsultation.demo');

// Route temporaire pour la démo de prescription de médicaments
Route::get('/prescription-demo', function() {
    return view('prescriptions.demo');
})->name('prescriptions.demo');

// Route temporaire pour la démo d'enregistrement de dossier médical patient
Route::get('/medical-record-demo', function() {
    return view('medical-records.demo');
})->name('medical-records.demo');

// Route de test simple
Route::get('/test', function() { return 'Test OK'; });
// Route de test de vue
Route::get('/test-view', function() {
    $doctors = \App\Models\Doctor::with('user')->get();
    return view('welcome', compact('doctors'));
});
Route::get('/test-login', function() { return view('auth.login'); });
Route::get('/test-admin-login', function() { return view('auth.admin-login'); });

// Gestion des rendez-vous pour l'admin
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/appointments', [\App\Http\Controllers\Admin\DashboardController::class, 'appointments'])->name('admin.appointments');
    Route::delete('/admin/appointments/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'destroyAppointment'])->name('admin.appointments.delete');
    Route::get('/admin/appointments/{id}/edit', [\App\Http\Controllers\Admin\DashboardController::class, 'editAppointment'])->name('admin.appointments.edit');
    Route::post('/admin/appointments/{id}/update', [\App\Http\Controllers\Admin\DashboardController::class, 'updateAppointment'])->name('admin.appointments.update');
});

// Route temporaire pour reset le mot de passe d'un utilisateur spécifique
Route::get('/dev-reset-mdp', function() {
    $u = \App\Models\User::where('email', 'mediconnect.bj@gmail.com')->first();
    if ($u) {
        $u->password = bcrypt('nouveaumdp2024');
        $u->save();
        return 'Mot de passe réinitialisé !';
    }
    return 'Utilisateur non trouvé.';
});

// Route temporaire pour corriger le nom du rôle id=3 en 'patient'
Route::get('/dev-fix-role-patient', function() {
    $role = \App\Models\Role::find(3);
    if ($role) {
        $role->name = 'patient';
        $role->save();
        return 'Rôle corrigé : id=3 => patient';
    }
    return 'Rôle id=3 non trouvé.';
});

// Route temporaire pour corriger complètement le compte patient
Route::get('/dev-fix-patient', function() {
    $user = \App\Models\User::where('email', 'mediconnect.bj@gmail.com')->first();
    $role = \App\Models\Role::where('name', 'patient')->first();
    if ($user && $role) {
        $user->password = bcrypt('nouveaumdp2024');
        $user->role_id = $role->id;
        if (isset($user->active_status)) $user->active_status = 1;
        $user->save();
        return 'Patient corrigé : mot de passe = nouveaumdp2024, rôle = patient, compte activé.';
    }
    return 'Utilisateur ou rôle patient non trouvé.';
});

Route::get('/admin/quick-login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'quickLogin'])->name('admin.quicklogin');
Route::get('/bills/{bill}/receipt', [FedaPayController::class, 'downloadReceipt'])->name('bills.receipt.pdf');
Route::get('/prescriptions/{prescription}/pdf', [App\Http\Controllers\PrescriptionController::class, 'downloadPdf'])->name('prescriptions.pdf');