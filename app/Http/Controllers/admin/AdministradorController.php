<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Convenio;
use App\Models\Convenio_actual;

class AdministradorController extends Controller
{
    public static function adminView(Request $request)
    {
        $convenios = Convenio::getConvenios();
        return view('presupuestos.admin', compact('convenios'));
    }

    public static function updateConvenio(Request $request)
    {
        $convenio = new Convenio_actual();

        $convenio->convenio_id = $request->convenio_id;
        $convenio->nombre_convenio = $request->nombre_convenio;

        $convenio->save();

        return redirect()->route('presupuestos.index')->with('success', 'Cambios guardados exitosamente');
    }
}
