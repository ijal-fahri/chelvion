<?php

namespace App\Http\Controllers\Pelanggan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BantuanPelangganController extends Controller
{
    public function index() {
        return view('pelanggan.bantuan.index');
    }
}
