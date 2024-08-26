<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Convenio;
use App\Http\Controllers\Controller;

class ConvenioController extends Controller
{
    public function getConvenios(Request $request)
    {
        $obraSocial = $request->get('obra_social');
        //dd($obraSocial);
        
        $convenios = Convenio::where('fin_vigencia', '>=', now())
            ->where('nombre', 'like', '%' . $obraSocial . '%')
            ->where('borrado_logico', false)
            ->get();
        return response()->json($convenios);
    }
}
