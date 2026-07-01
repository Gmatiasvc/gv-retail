<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\User;

class Venta extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'fecha_emision',
        'cliente_id',
        'cajero_id',
        'total',
        'descuento',
        'puntos_usados',
        'pago_recibido',
        'cambio',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'cajero_id');
    }
}
