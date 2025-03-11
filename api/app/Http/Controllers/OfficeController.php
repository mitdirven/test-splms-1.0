<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function getAllOffices()
    {
        $offices = Office::all();
        return response([
            'offices' => $offices
        ]);
    }
}
