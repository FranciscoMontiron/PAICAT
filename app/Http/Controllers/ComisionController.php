<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComisionController extends Controller
{
    public function index()
    {
        return view('comisiones.index');
    }
}
