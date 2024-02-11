<?php

namespace App\Http\Controllers;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\LogAct;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(){
        return view('admin.user.index');
    }

    public function getData(Request $request){
        if ($request->ajax()) {
            $log = User::where('role','!=','admin')->get();

            return DataTables::of($log)
                ->editColumn('name',function($query){
                    return $query->name;
                })
                ->editColumn('role',function($query){
                    return $query->role;
                })
                ->editColumn('username',function($query){
                    return $query->username;
                })
                ->editColumn('email',function($query){
                    return $query->email;
                })
                ->editColumn('opsi', function($query){
                    $edit_query = route('admin.user.edit', $query->id);
                    $delete_query = route('admin.user.delete',['id'=>$query->id]);

                    $form = '<form method="POST" action="'.$delete_query.'">'.csrf_field().'<input type="submit" class="btn btn-danger btn-sm m-1" onclick="return confirm(\'Data Akan Dihapus?\')" value="Delete"></form>';
                    
                    return 
                    '<div class="d-flex"><a href="'.$edit_query.'" class="btn btn-primary btn-sm m-1">Edit</a>
                    '.$form.'</div>';
                })
                ->rawColumns(['name','role','username','email','opsi'])
                ->make(true);
        }
    }

    public function create(){
        return view('admin.user.create');
    }

    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|string|min:6',
            'role' => 'required',
            'username' => 'required|string|min:6|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {

            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Menambah data user';
            $log->time = $date->toDateTimeString();
            $log->save();

            $user = new User;
            $user->name = $request->name;
            $user->role = $request->role;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            return redirect()->route('admin.user.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }
    public function edit(Request $request){
        $user = User::find($request->id);
        return view('admin.user.edit',[
            'user' => $user
        ]);
    }

    public function update(Request $request){
        
        $user = User::find($request->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|string|min:6|max:255|'.($user->username != $request->username? 'unique:users': ''),
            'email' => 'required|string|email|max:255|'.($user->email != $request->email? 'unique:users': ''),
        ]);

        if ($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $date = Carbon::now();

            $log = new LogAct;
            $log->nama = auth()->user()->name;
            $log->keterangan = auth()->user()->name.' Mengubah data user';
            $log->time = $date->toDateTimeString();
            $log->save();

            
            $user->name = $request->name;
            $user->role = $request->role == null ? $user->role : $request->role;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password == null ? $user->password : bcrypt($request->password);
            $user->update();
            
            if(auth()->user()->role == 'admin'){
                return redirect()->route('admin.user.index')->with(['message' => 'Berhasil Mengubah data.']);
            }
            else{
                return redirect()->route('admin.user.edit',['id' => $user->id])->with(['message' => 'Berhasil Mengubah data.']);
            }

        }
    }

    public function destroy(User $user,Request $request){
        $user = User::find($request->id);
        $user->delete();
        return redirect()->back()->with(['message' => 'Berhasil menghapus data.']);
    }
}
