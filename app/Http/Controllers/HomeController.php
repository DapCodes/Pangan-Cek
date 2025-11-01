<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Cek role user
        if ($user->role === 'USER') {
            // Jika user biasa, arahkan ke halaman peta
            return redirect()->route('map.index');
        } elseif ($user->role === 'ADMIN') {
            // Jika admin, tampilkan dashboard
            return view('home');
        } else {
            // Default fallback (jika role lain)
            abort(403, 'Unauthorized action.');
        }
    }
}
