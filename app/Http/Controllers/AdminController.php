<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login( Request $request){
   

        if ($request -> isMethod('post')){
            $data = $request->input();
            if(Auth::attempt(['email'=> $data['email'], 'password'=>$data['password'], 'admin'=>'1'])){
                Session::put('adminSession', $data['email']);
                return redirect('admin/dashboard');
            }else{
                //echo "Failed"; die;
                return redirect('/admin')->with('flash_message_error', 'Invalid Username or Password');
            }
        }
        return view('admin.admin_login');
    }

    public function dashboard(){
        if(Session::has('adminSession')){
            //perform admin tasks
        }
        else{
            return redirect('/admin')->with('flash_message_error', 'Please login to access');
        }
        return view('admin.dashboard');
    }

    public function settings(){
        if(Session::has('adminSession')){
            //perform settings tasks
        }
        else{
            return redirect('/admin')->with('flash_message_error', 'Please login to access');
        }
        return view ('admin.settings');
    }    

    public function chkPassword(Request $request){
        if (Session::has('adminSession')){
            $data = $request->all();
            $current_password = $data['current_pwd'];
            $check_password = User::where(['admin'=>'1'])->first();
            if(Hash::check($current_password, $check_password->password)){
                echo "true"; die;
            }
            else{
                echo "false"; die;
            }
        }
        else{
            return redirect('/admin')->with('flash_message_error', 'Please login to access');
        }
    }
    
    public function updatePassword(Request $request){
        if (Session::has('adminSession')){
            if($request->isMethod('post')){
                $data = $request->all();
                // echo "<pre>";
                // print_r($data);
                // die;
                $check_password = User::where(['email'=>Auth::user()->email])->first();
                $current_password = $data['current_pwd'];
                if(Hash::check($current_password, $check_password->password)){
                    $password = bcrypt($data['new_pwd']);
                    User::where('id', '1')->update(['password' => $password]);
                    return redirect('/admin/settings')->with('flash_message_success', 'Password updated Successfully!');
                }
                else{
                    return redirect('/admin/settings')->with('flash_message_error', 'Password updated Failed!');
                }
            }
        }
        else{
            return redirect('/admin')->with('flash_message_error', 'Please login to access');
        }
    }
    public function logout(){
        Session::flush();
        return redirect('/admin')->with('flash_message_success', 'Logged out Successfully');
    }
}
