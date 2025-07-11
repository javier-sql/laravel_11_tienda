<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Formulario de registro
    public function showRegistrationForm(){
        return view('auth.register');
    }
    // Registro de usuario
    public function register(Request $request){
        // validacion de los datos formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);

        // rol por defecto
        $role = Role::where('name', 'usuario')->first();

        // crear al usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        // Iniciar sesión para el nuevo usuario
        // auth()->login($user);

        return redirect('/inicio');
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
}
