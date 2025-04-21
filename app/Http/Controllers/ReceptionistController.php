<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Receptionist;
use App\Models\Client;

class ReceptionistController extends Controller
{
    //
    public function testApprovedClients()
    {
        $receptionist = Receptionist::find(1);
    
        if (!$receptionist) {
            return response()->json(['error' => 'Receptionist not found'], 404);
        }
    
        dd($receptionist->approvedClients()->get());
    }
    
}
