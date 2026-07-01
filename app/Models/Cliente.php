<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'tipo_documento',
        'num_documento',
        'razon_social',
        'email',
        'telefono',
        'puntos',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
