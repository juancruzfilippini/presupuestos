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

}