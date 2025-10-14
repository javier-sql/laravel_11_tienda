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

class AuthController extends Controller
{
    // Mostrar formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Registrar usuario
public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // Validar el email usando la API de Brevo
    $response = Http::withHeaders([
        'api-key' => env('BREVO_API_KEY'),
        'Accept' => 'application/json',
    ])->get('https://api.brevo.com/v3/contacts/' . urlencode($request->email));

    if ($response->status() === 404) {
        return back()->withErrors(['email' => 'El correo ingresado no parece ser válido o no existe.']);
    }

    // Si pasa la validación, registrar el usuario normalmente
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'activation_token' => Str::random(60),
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
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
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
        return redirect('/inicio')->with('info', 'Tu cuenta ya está activada. Puedes iniciar sesión.');
    }

    $user->is_active = true;
    $user->activation_token = null;
    $user->save();

    return redirect('/inicio')->with('success', 'Cuenta activada correctamente. Ya puedes iniciar sesión.');
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
