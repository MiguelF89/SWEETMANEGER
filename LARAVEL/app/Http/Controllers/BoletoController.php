<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoletoController extends Controller
{
    /**
     * Display the boleto reader page.
     */
    public function reader()
    {
        return view('boleto.reader');
    }
}