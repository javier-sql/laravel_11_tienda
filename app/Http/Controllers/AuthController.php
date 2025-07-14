<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;  
use App\Mail\ActivationEmail;    
use Illuminate\Support\Str;      

class AuthController extends Controller
{
    // Formulario de registro
    public function showRegistrationForm(){
        return view('auth.register');
    }
    // Registro de usuario
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $role = Role::where('name', 'usuario')->first();

        $activation_token = Str::random(64);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'activation_token' => $activation_token,
            'is_active' => false,
        ]);

        // enviar mail de activación
        Mail::to($user->email)->send(new ActivationEmail($user));

        return redirect('/inicio')->with('success', 'Revisa tu correo para activar tu cuenta.');
    }

    // Formulario de inicio de sesión
    public function showLoginForm(){
        return view('auth.login');
    }
    // Procesa el inicio de sesión
    public function login(Request $request){
        // Validación de los datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        // Intento de autenticación
        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Debes activar tu cuenta antes de iniciar sesión.']);
            }

            $request->session()->regenerate();
            return redirect()->intended('/inicio');
        }

        // Si la autenticación falla
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
        }    
        // Cierra la sesión del usuario
        public function logout(Request $request){
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/inicio');
    }

    public function activateAccount($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect('/inicio')->with('error', 'Token de activación inválido.');
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return redirect('/inicio')->with('success', 'Cuenta activada correctamente. Ya puedes iniciar sesión.');
    }

}
