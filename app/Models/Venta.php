<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $primaryKey = 'IdVenta';
    
    
    protected $fillable = [
        'Fecha',
        'Total',
        'Destino',
        'IdCliente', 
    ];

    
    public $timestamps = false;

    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'IdCliente');
    }
}
