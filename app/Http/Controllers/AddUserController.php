<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Validator;


class AddUserController extends Controller
{

    
    // possibly unused?
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'usertype' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
    }
    
    // possibly unused?
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'usertype' => $data['usertype'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function store(Request $req) {
        
        $validator = Validator::make($req->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'usertype' => 'required',
            'password' => 'required|confirmed'
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->action('AddUserController@index')->withErrors($messages);
        } else {
            $user = new User;
            $user->name = $req->name;
            $user->email = $req->email;
            $user->password = bcrypt($req->password);
            $user->usertype = $req->usertype;
            $user->save();
            return redirect()->action('AddUserController@index')->with('message', 'User added successfully.');
        }
    }

    public function deleteAdminUser(Request $req) {
        $user = User::find($req->modal_adminUserId_delete);
        if ($user) {
            $user->delete();
        }
        return redirect()->action('AddUserController@index')->with('message', 'User deleted successfully.');
    }

    public function editUser(Request $req) {
        if (User::find($req->modal_user_edit)) {
            $user = User::find($req->modal_user_edit);
            $user->name = $req->modal_name_edit;
            $user->email = $req->modal_email_edit;
            $user->save();
        }
        return redirect()->action('AddUserController@index');
    }

    public function index(){
        $admins = DB::table('users')
            ->where('usertype', 'admin')
            ->get();
        $staffs = DB::table('users')
            ->where('usertype', 'staff')
            ->get();
        $students = DB::table('users')
            ->where('usertype', 'student')
            ->get();
        return view('pages.addUser',compact('admins', 'staffs', 'students'));
    }

}
