<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        return "Halo! Ini adalah Halaman Utama (Home) Campus Lost & Found Portal.";
    }

    // Pastikan fungsi ini ada di dalam file ItemController.php kamu
    public function catalog()
    {
        return "Ini adalah Halaman Katalog Daftar Barang Hilang & Temuan.";
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }
}