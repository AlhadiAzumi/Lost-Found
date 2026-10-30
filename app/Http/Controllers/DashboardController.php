<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengarahkan ke view dashboard bawaan Breeze (resources/views/dashboard.blade.php)
        return view('dashboard');
    }
}