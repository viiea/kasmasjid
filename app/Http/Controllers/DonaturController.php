<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\LogAct;
use App\Models\Donatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DonaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donatur = Donatur::all();
        return view('donatur.index',[
            'donatur' => $donatur
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('donatur.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'no_telephone' => 'required',
            'file_upload' => 'required|file'
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {

            $cek_file = $request->file('file_upload')->getClientOriginalName();
            $cek_type = $request->file('file_upload')->getClientOriginalExtension();
            
            $path = $request->file('file_upload')->move(public_path('assets/donatur/uploads'), $cek_file);

            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' manambah data '.$request->nama;
            $log->time = $date->toDateTimeString();
            $log->save();

            $donatur = new Donatur;
            $donatur->nama = $request->nama;
            $donatur->alamat = $request->alamat;
            $donatur->no_telephone = $request->no_telephone;
            $donatur->upload = $cek_file;

            $donatur->save();
            return redirect()->route('donatur.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donatur $donatur, Request $request)
    {
        $donatur = Donatur::find($request->id);
    
        return view('donatur.edit',[
            'donatur' => $donatur
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donatur $donatur)
    {

        $donatur_update = Donatur::find($request->id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'no_telephone' => 'required',
            'file_upload' => 'file'
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {

            if($request->hasFile('file_upload')){
                $cek_file = $request->file('file_upload')->getClientOriginalName();
                $cek_type = $request->file('file_upload')->getClientOriginalExtension();
                
                $path = $request->file('file_upload')->move(public_path('assets/donatur/uploads'), $cek_file);
                
            }else{
                $cek_file = $donatur_update->upload;
            }

            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' mengubah data '.$donatur_update->nama. ' ke '.$request->nama;
            $log->time = $date->toDateTimeString();
            $log->save();

            $donatur_update->nama = $request->nama;
            $donatur_update->alamat = $request->alamat;
            $donatur_update->no_telephone = $request->no_telephone;
            $donatur_update->upload = $cek_file;

            $donatur_update->save();
            return redirect()->route('donatur.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donatur $donatur)
    {
        $donatur = Donatur::find($donatur->id);

        $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Menghapus data '.$donatur->nama;
            $log->time = $date->toDateTimeString();
            $log->save();

        $donatur->delete();
        return redirect()->route('donatur.index')->with(['message' => 'Berhasil menghapus data.']);
    }

    public function download(Request $request){
        $donatur = Donatur::find($request->id);
        $file = public_path('assets/uploads/donatur/'.$donatur->upload);
        return Response::download($file);
    }
}
