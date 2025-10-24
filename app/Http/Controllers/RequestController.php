<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Menampilkan halaman daftar permintaan (request).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Baris ini memberitahu Laravel untuk mencari dan menampilkan file
        // di resources/views/request/index.blade.php
        return view('admin.request.index');
    }
}
