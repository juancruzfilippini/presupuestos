<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Convenio extends Model
{
    // Usa la conexión por defecto (db-sistema-ap)
    protected $connection = 'db2'; // Para la base de datos 'db-sistema-ap'
    protected $table = 'convenio'; // Nombre de la tabla en la base de datos



    protected $fillable = [
        'id',
        'nombre',
        'fin_vigencia',
        'borrado_logico',
    ];

    public static function getConvenioById($id)
    {
        // Buscar el registro por ID
        $convenio = self::where('id', $id)->first();

        // Devolver el nombre si el registro existe, de lo contrario null
        return $convenio ? $convenio->nombre : null;
    }

    public static function getConvenios()
    {
        $convenios = Convenio::where('fin_vigencia', '>=', now())
            ->where('nombre', 'like', '%particular completo%')
            ->where('borrado_logico', false)
            ->get();
        return $convenios->toArray();
    }
}