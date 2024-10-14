<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Convenio;
use App\Models\Convenio_actual;
use App\Models\Users;
use App\Models\Rol;

class AdministradorController extends Controller
{
    public static function adminView(Request $request)
    {
        $convenios = Convenio::getConvenios();
        $convenioActual = Convenio_actual::orderBy('id', 'desc')->first();
        $usuarios = Users::all();
        $roles = Rol::all();
        return view('presupuestos.admin', compact('convenios', 'usuarios', 'roles', 'convenioActual'));
    }

    public static function updateConvenio(Request $request)
    {
        $convenio = new Convenio_actual();

        $convenio->convenio_id = $request->convenio_id;
        $convenio->nombre_convenio = $request->nombre_convenio;
        $convenio->save();

        $usuariosIds = $request->input('usuarios_ids');
        $roles = $request->input('roles');

        // Recorrer los arrays y actualizar los roles de los usuarios
        foreach ($usuariosIds as $index => $usuarioId) {
            $usuario = Users::findOrFail($usuarioId);
            $usuario->rol_id = $roles[$index];
            $usuario->save();
        }
        return redirect()->route('presupuestos.index')->with('success', 'Cambios guardados exitosamente');
    }
}
