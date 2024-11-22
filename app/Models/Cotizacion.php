<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $primaryKey = 'IdCotizacion';
    protected $fillable = [
        'Fecha',
        'Total',
        'Destino',
        'IdCliente', 
    ];

    public $timestamps = false;
    protected $table = 'cotizaciones'; 
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'IdCliente');
    }
}
