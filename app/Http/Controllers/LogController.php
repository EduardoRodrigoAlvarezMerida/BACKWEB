<?php

namespace App\Http\Controllers;

use App\Models\LogInventario;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        try {
            
            $logs = LogInventario::orderBy('IdLogs', 'desc')->get();

            return response()->json($logs, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los logs'], 500);
        }
    }
}
