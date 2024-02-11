<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use Illuminate\Http\Request;
use Validator;

class JenisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenis = Jenis::all();
        return view('jenis.index',[
            'jenis' => $jenis
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jenis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $jenis = new Jenis;
            $jenis->nama = $request->nama;
            $jenis->status = $request->status;
            $jenis->save();
            return redirect()->route('jenis.index')->with(['message' => 'Berhasil menambah data.']);

        }
        // dd($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Jenis $jenis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jenis $jenis, Request $request)
    {
        $jeniss = Jenis::find($request->id);

        return view('jenis.edit',[
            'jenis' => $jeniss
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jenis $jenis)
    {
        $jeniss = Jenis::find($request->id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $jeniss->update([
                'nama' => $request->nama,
                'status' => $request->status
            ]);
            return redirect()->route('jenis.index')->with(['message' => 'Berhasil mengubah data.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jenis $jenis, Request $request)
    {
        $jeniss = Jenis::find($request->id);
        $jeniss->delete();
        return redirect()->route('jenis.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}
