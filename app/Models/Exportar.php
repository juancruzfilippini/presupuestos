<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Exportar extends Model
{
    public function getPresupuesto($id)
    {
        // Ejecutar la consulta
        $presupuesto = DB::connection('mysql')->table('presupuesto as p')
            ->leftJoin('firmas as f', 'p.id', '=', 'f.presupuesto_id')
            ->where('p.id', 116)
            ->select(
                'p.*',  // Todos los campos de la tabla presupuesto
                'f.comercializacion as fcomer',  // Campos de la tabla firmas
                'f.auditoria as faudi',
                'f.direccion as fdirec'
            )->first();

        //dd($presupuesto);

        return $presupuesto;
    }
}