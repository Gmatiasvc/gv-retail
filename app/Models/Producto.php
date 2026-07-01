<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\VentaDetalle;

class Producto extends Model
{
    protected $fillable = [
        'barcode',
        'nombre',
        'precio',
        'stock',
        'imagen',
    ];

    protected $appends = [
        'imagen_url',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function getImagenUrlAttribute()
    {
        if (! $this->imagen) {
            return null;
        }

        return Storage::disk('public')->url($this->imagen);
    }
}
