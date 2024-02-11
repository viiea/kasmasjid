<?php

namespace App\Http\Controllers;

use App\Models\LogAct;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LogActController extends Controller
{
    public function index(){
        return view('Log.index');
    }

    public function getData(Request $request){
        if ($request->ajax()) {
            $log = LogAct::all();

            return DataTables::of($log)
                ->editColumn('nama',function($query){
                    return $query->nama;
                })
                ->editColumn('keterangan',function($query){
                    return $query->keterangan;
                })
                ->editColumn('time', function($query){
                    return $query->time;
                })
                
                ->rawColumns(['nama','keterangan','time'])
                ->make(true);
        }
    }
}
