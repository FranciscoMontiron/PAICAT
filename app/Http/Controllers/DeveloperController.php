<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    /**
     * Mostrar página de herramientas para desarrolladores
     */
    public function index()
    {
        // Solo permitir en desarrollo
        if (!config('app.debug')) {
            abort(404);
        }

        return view('developer.index');
    }
}
