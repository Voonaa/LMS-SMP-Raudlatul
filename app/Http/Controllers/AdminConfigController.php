<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminConfigController extends Controller
{
    public function index()
    {
        return view('admin.config.index');
    }

    public function save(Request $request)
    {
        // Placeholder for future config saving
        return redirect()->route('admin.config.index')->with('success', 'Konfigurasi berhasil disimpan (Simulasi).');
    }
}
