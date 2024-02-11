<?php

namespace App\Http\Controllers;

use PDF;
use Validator;
use Carbon\Carbon;
use App\Models\Jenis;
use App\Models\LogAct;
use App\Models\Donatur;
use App\Models\Pemasukan;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Response;

class PemasukanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pemasukan.index');
    }

    public function getData(Request $request){

        if ($request->ajax()) {
            $pemasukan = Pemasukan::all();

            return DataTables::of($pemasukan)
                ->editColumn('nama', function ($pemasukan) {
                    if($pemasukan->donatur == null){
                        return '-';
                    }
                    else{
                        return $pemasukan->donatur->nama;
                    }
                })
                ->editColumn('nama_jenis', function ($pemasukan) {
                    if($pemasukan->jenis == null){
                        return '-';
                    }
                    else{
                        return $pemasukan->jenis->nama;
                    }
                })
                ->editColumn('tanggal_pemasukan',function($pemasukan){
                    return $pemasukan->tanggal_pemasukan;
                })
                ->editColumn('jumlah_pemasukan',function($pemasukan){
                    return $pemasukan->jumlah_pemasukan;
                })
                ->editColumn('opsi', function($pemasukan){
                    $edit_pemasukan = route('pemasukan.edit', $pemasukan->id);
                    $delete_pemasukan = route('pemasukan.delete',['id'=>$pemasukan->id]);
                    $download = route('pemasukan.download',['id'=>$pemasukan->id]);

                    $cek = auth()->user()->role == 'guest' ? 'd-none' : '';
                    $cek_2 = auth()->user()->role != 'admin' ? 'd-none' : '';

                    $a_link = '<a href="'.$edit_pemasukan.'" class="btn btn-primary btn-sm m-1 '.$cek.'">Edit</a>';
                    $form = '<form class="'.$cek_2.'" method="POST" action="'.$delete_pemasukan.'">'.csrf_field().'<input type="submit" onclick="return confirm(\'Data Akan Dihapus?\')" class="btn btn-danger btn-sm m-1" value="Delete"></form>';
                    $download_link = '<a target="_blank" href="'.$download.'" class="btn btn-primary btn-sm m-1">Download</a>';
                    return
                    '<div class="d-flex">'.$a_link.''.$form.''.$download_link.'</div>';
                })
                ->rawColumns(['opsi','nama','nama_jenis','tanggal_pemasukan','jumlah_pemasukan'])
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenis = Jenis::select('id', 'nama')->where('status','=', 2)->get();
        $donatur = Donatur::select('id', 'nama')->get();
        return view('pemasukan.create',[
            'donatur' => $donatur,
            'jenis' => $jenis
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donatur_id' => 'required',
            'jenis_id' => 'required',
            'jumlah_pemasukan' => 'required',
            'file_upload' => 'required|file',
            'tanggal_pemasukan' => 'required'
        ]);
        

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {

            $cek_file = $request->file('file_upload')->getClientOriginalName();
            $cek_type = $request->file('file_upload')->getClientOriginalExtension();
            $path = $request->file('file_upload')->move(public_path('assets/uploads/pemasukan'), $cek_file);

            $pemasukan = new Pemasukan;
            $pemasukan->id_donatur = $request->donatur_id;
            $pemasukan->id_jenis = $request->jenis_id;
            $pemasukan->jumlah_pemasukan = $request->jumlah_pemasukan;
            $pemasukan->upload = $cek_file;
            $pemasukan->tanggal_pemasukan = $request->tanggal_pemasukan;
            $pemasukan->save();

            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Menambah Pemasukan dengan nominal '.$request->jumlah_pemasukan;
            $log->time = $date->toDateTimeString();
            $log->save();
            return redirect()->route('pemasukan.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pemasukan $pemasukan, Request $request)
    {
        $jenis = Jenis::select('id', 'nama')->where('status','=', 2)->get();
        $donatur = Donatur::select('id', 'nama')->get();
        $pemasukans = Pemasukan::find($request->id);
        return view('pemasukan.edit',[
            'pemasukan' => $pemasukans,
            'donatur' => $donatur,
            'jenis' => $jenis
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pemasukan $pemasukan)
    {
        $pemasukan = Pemasukan::find($request->id);

        $validator = Validator::make($request->all(), [
            'donatur_id' => 'required',
            'jenis_id' => 'required',
            'jumlah_pemasukan' => 'required',
            'tanggal_pemasukan' => 'required'
        ]);
        

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {

            if($request->hasFile('file_upload')){
                $cek_file = $request->file('file_upload')->getClientOriginalName();
                $cek_type = $request->file('file_upload')->getClientOriginalExtension();
               
                $path = $request->file('file_upload')->move(public_path('assets/uploads/pemasukan'), $cek_file);
                
            }else{
                $cek_file = $pemasukan->upload;
            }

            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Mengupdate Pemasukan data '.$pemasukan->jumlah_pemasukan.' menjadi nominal '.$request->jumlah_pemasukan;
            $log->time = $date->toDateTimeString();
            $log->save();

            $pemasukan->id_donatur = $request->donatur_id;
            $pemasukan->id_jenis = $request->jenis_id;
            $pemasukan->jumlah_pemasukan = $request->jumlah_pemasukan;
            $pemasukan->upload = $cek_file;
            $pemasukan->tanggal_pemasukan = $request->tanggal_pemasukan;
            $pemasukan->update();


            
            return redirect()->route('pemasukan.index')->with(['message' => 'Berhasil mengubah data.']);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemasukan $pemasukan,Request $request)
    {
        $pemasukan = Pemasukan::find($request->id);

        $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Menghapus data '.$pemasukan->jumlah_pemasukan.' dari donatur '.$pemasukan->donatur->nama;
            $log->time = $date->toDateTimeString();
            $log->save();

        $pemasukan->delete();
        return redirect()->route('pemasukan.index')->with(['message' => 'Berhasil menghapus data.']);
    }

    public function getPDF(){

        $pemasukan = Pemasukan::all();
        
        $pdf = PDF::loadview('pemasukan.pdf',[
            'pemasukan' => $pemasukan
        ]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan Pemasukan Kas Masjid.pdf');

    }

    public function download(Request $request){
        
        $pemasukan = Pemasukan::find($request->id);
        $file = public_path('assets/uploads/pemasukan/'.$pemasukan->upload);
        return Response::download($file);
    }
}
