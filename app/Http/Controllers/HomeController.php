<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $request->session()->put('login', 'Selamat Datang.');

        $nominal_pemasukan = Pemasukan::select('jumlah_pemasukan')->sum('jumlah_pemasukan');
        $nominal_pengeluaran = Pengeluaran::select('jumlah_pengeluaran')->sum('jumlah_pengeluaran');
        $current_kas = $nominal_pemasukan - $nominal_pengeluaran;
        $request->session()->put('login', 'Selamat Datang.');
        return view('guest.index',[
            'nominal_pemasukan' => $nominal_pemasukan,
            'nominal_pengeluaran' => $nominal_pengeluaran,
            'current_kas' => $current_kas
        ]);
    }

    public function adminHome(Request $request){
        $request->session()->put('login', 'Selamat Datang Bendahara.');

        $nominal_pemasukan = Pemasukan::select('jumlah_pemasukan')->sum('jumlah_pemasukan');
        $nominal_pengeluaran = Pengeluaran::select('jumlah_pengeluaran')->sum('jumlah_pengeluaran');
        $current_kas = $nominal_pemasukan - $nominal_pengeluaran;
        return view('admin.index', [
            'nominal_pemasukan' => $nominal_pemasukan,
            'nominal_pengeluaran' => $nominal_pengeluaran,
            'current_kas' => $current_kas
        ]);
    }

    public function ketuaHome(Request $request){
        $request->session()->put('login', 'Selamat Datang Ketua.');

        $nominal_pemasukan = Pemasukan::select('jumlah_pemasukan')->sum('jumlah_pemasukan');
        $nominal_pengeluaran = Pengeluaran::select('jumlah_pengeluaran')->sum('jumlah_pengeluaran');
        $current_kas = $nominal_pemasukan - $nominal_pengeluaran;
        return view('ketua.index', [
            'nominal_pemasukan' => $nominal_pemasukan,
            'nominal_pengeluaran' => $nominal_pengeluaran,
            'current_kas' => $current_kas
        ]);
    }

    public function getData(Request $request){

    }
}
