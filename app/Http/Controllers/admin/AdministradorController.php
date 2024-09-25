<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Convenio;

class AdministradorController extends Controller
{
    public static function adminView(Request $request){

        $convenios = Convenio::getConvenios();

        return view('presupuestos.admin', compact('convenios'));
    }
}
