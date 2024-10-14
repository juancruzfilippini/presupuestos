<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anestesia extends Model
{

    protected $table = 'anestesia';
    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'
    use HasFactory;

    public static function getAnestesiaById($id){
    $anestesia = self::find($id);
    return $anestesia->nombre;
}

    
}
