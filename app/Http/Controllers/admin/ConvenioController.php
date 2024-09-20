<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Convenio;
use App\Http\Controllers\Controller;

class ConvenioController extends Controller
{
    public function getConvenios()
    {
        $convenios = Convenio::where('fin_vigencia', '>=', now())
            ->where('nombre', 'like', '%particular%')
            ->where('borrado_logico', false)
            ->get();
        return $convenios;
    }
}
