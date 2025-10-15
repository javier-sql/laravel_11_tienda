<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AddressService;
use App\Models\Commune;

class AuthController extends Controller
{

    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }


    // Mostrar formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Registrar usuario
// public function register(Request $request)
// {
//     $request->validate([
//     'name' => 'required|string|max:255',
//     'email' => 'required|string|email|max:255|unique:users',
//     'password' => 'required|string|min:2|confirmed',
//     ], [
//         'name.required' => 'El nombre es obligatorio.',
//         'name.string' => 'El nombre debe ser un texto.',
//         'name.max' => 'El nombre no puede tener más de 255 caracteres.',

//         'email.required' => 'El correo es obligatorio.',
//         'email.string' => 'El correo debe ser un texto.',
//         'email.email' => 'El correo debe ser una dirección válida.',
//         'email.max' => 'El correo no puede tener más de 255 caracteres.',
//         'email.unique' => 'Este correo ya está en uso.',

//         'password.required' => 'La contraseña es obligatoria.',
//         'password.string' => 'La contraseña debe ser un texto.',
//         'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
//         'password.confirmed' => 'Las contraseñas no coinciden.',
//     ]);


//     $domain = explode('@', $request->email)[1] ?? null;
//     if (!$domain || !checkdnsrr($domain, 'MX')) {
//         return back()->withErrors(['email' => 'El dominio del correo no existe.'])->withInput();
//     }

//     // Si pasa la validación, registrar el usuario normalmente
//     $user = User::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'password' => bcrypt($request->password),
//         'activation_token' => Str::random(60),
//         'role_id' => 1,
//         'is_active' => false,
//     ]);
    

//     $this->sendActivationEmail($user);

//     return redirect()->route('login')->with('success', 'Cuenta creada. Revisa tu correo para activarla.');
// }

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6',

        // Validación de dirección
        'commune_id' => 'required|exists:communes,id',
        'street' => 'required|string|max:255',
        'number' => 'required|string|max:10',
        // 'unit' => 'nullable|string|max:50',
        'property_type' => 'required|string|max:50',
        'property_number' => 'nullable|string|max:50',
        'phone' => 'required|string|max:20',
    ],[
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser un texto.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',

        'email.required' => 'El correo es obligatorio.',
        'email.string' => 'El correo debe ser un texto.',
        'email.email' => 'El correo debe ser una dirección válida.',
        'email.max' => 'El correo no puede tener más de 255 caracteres.',
        'email.unique' => 'Este correo ya está en uso.',

        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser un texto.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
    ]);

    $commune = Commune::find($request->commune_id);

    if (!$commune) {
        return back()->withErrors([
                'commune_id' => 'La comuna seleccionada no es válida.'
        ])->withInput();
    }


    $domain = explode('@', $request->email)[1] ?? null;
    if (!$domain || !checkdnsrr($domain, 'MX')) {
        return back()->withErrors(['email' => 'El dominio del correo no existe.'])->withInput();
    }


    // Validar dirección usando el servicio
    $isValid = $this->addressService->validateCommuneAddress(
            $commune->name,
            $request->street,
            $request->number
    );

    if (!$isValid) {
        return back()->withErrors([
            'street' => 'La dirección no coincide con la comuna seleccionada o numero. Si es Avenida comience con "Av." o "Avenida". Si es Pasaje comience con "Pje." o "Pasaje". Si es calle, no use prefijos.'
        ])->withInput();
    }


    // Crear usuario
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'activation_token' => Str::random(60),
        'role_id' => 1,
        'is_active' => false,
        'phone' => $request->phone,
        'street' => $request->street,
        'number' => $request->number,
        'unit' => $request->property_number,
        'commune_id' => $request->commune_id,
        'property_type'=> $request->property_type,
    ]);


    $this->sendActivationEmail($user);

    return redirect()->route('login')->with('success', 'Cuenta creada. Revisa tu correo para activarla.');

}







    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Debes activar tu cuenta antes de iniciar sesión.']);
            }

            $request->session()->regenerate();
            return redirect()->intended('/inicio');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden.',
        ])->onlyInput('email');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/inicio');
    }

    // Activar cuenta al hacer clic en enlace
public function activateAccount($token)
{
    $user = User::where('activation_token', $token)->first();

    if (!$user) {
        return redirect('/inicio')->with('error', 'El enlace de activación es inválido o ya fue utilizado.');
    }

    // Si el usuario ya está activo, también evitamos reactivar
    if ($user->is_active) {
        return redirect('/login')->with('info', 'Tu cuenta ya está activada. Puedes iniciar sesión.');
    }

    $user->is_active = true;
    $user->activation_token = null;
    $user->save();

    return redirect('/login')->with('success', 'Cuenta activada correctamente. Ya puedes iniciar sesión.');
}


    // Enviar correo de activación usando API de Brevo
private function sendActivationEmail($user)
{
    try {
        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => env('BREVO_SENDER_NAME'),
                'email' => env('BREVO_SENDER_EMAIL')
            ],
            'to' => [
                ['email' => $user->email, 'name' => $user->name]
            ],
            'subject' => 'Activa tu cuenta',
            'htmlContent' => "<p>Hola {$user->name},</p>
                <p>Haz click en el siguiente enlace para activar tu cuenta:</p>
                <a href='" . url("/activar-cuenta/{$user->activation_token}") . "'>Activar cuenta</a>"
        ]);

        if ($response->successful()) {
            Log::info("Correo de activación enviado correctamente a {$user->email}");
        } else {
            Log::error("Error enviando correo de activación: " . $response->body());
        }

    } catch (\Exception $e) {
        Log::error("Excepción al enviar correo: " . $e->getMessage());
    }
}

}
