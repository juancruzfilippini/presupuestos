<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Users extends Model
{
    // Especifica si tu modelo tiene una tabla personalizada
    protected $table = 'users';
    protected $connection = 'mysql'; // Para la base de datos 'db-sistema-ap'


    /**
     * Busca un usuario por su ID y devuelve el campo 'name'.
     *
     * @param int $id
     * @return string|null
     */
    public static function getNameById(int $id): ?string
    {
        // Busca el usuario por ID
        $user = self::find($id);

        // Devuelve el campo 'name' o null si no se encuentra el usuario
        return $user ? $user->name : null;
    }
}
