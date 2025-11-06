<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    /**
     * Exibe a página de login
     */
    public function showLogin()
    {
        // Se já está logado, redireciona para o dashboard
        if (session()->has('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $token = trim($request->input('access_token'));

        // Verifica se o token existe
        $admin = AdminUser::verifyToken($token);

        if (!$admin) {
            return back()->withErrors([
                'access_token' => 'Token de acesso inválido.'
            ])->withInput();
        }

        // Atualiza o último login
        $admin->updateLastLogin();

        // Cria a sessão
        session([
            'admin_logged_in' => true,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Bem-vindo, ' . $admin->name . '!');
    }

    /**
     * Faz logout
     */
    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_id', 'admin_name']);
        session()->flush();

        return redirect()->route('admin.login')->with('success', 'Logout realizado com sucesso!');
    }
}

