<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;

class Inicio extends BaseController
{
    public function index()
    {
        return view('alimentacion/index');
    }
}