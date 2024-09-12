<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
    public function getUsuarioById($id)
    {
        // Busca el usuario por el ID
        $user = User::find($id);

        // Verifica si el usuario existe y devuelve su nombre
        if ($user) {
            return $user->name;
        } else {
            // Opcional: Maneja el caso en que el usuario no se encuentra
            return null; // O puedes lanzar una excepción o retornar un mensaje de error
        }
    }

    public static function getUserById($id)
    {
        $usuario = self::where('id', $id)->first();
        return $usuario ? $usuario->nombre : null;
    }

}
