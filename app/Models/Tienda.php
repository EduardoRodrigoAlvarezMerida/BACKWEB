<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;
    protected $primaryKey = 'IdTienda'; 
    protected $fillable = [
        'Nombre',
        'Direccion',
        'Latitud',
        'Longitud',
    ];
    public $timestamps = false;

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'IdTienda');
    }


}
