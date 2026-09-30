<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Sin sesión, la página de inicio manda directo al login.
        if (session('usuario_id') === null) {
            return redirect()->to('/login');
        }

        return view('home/dashboard', [
            'titulo' => 'Resumen'
        ]);
    }
}