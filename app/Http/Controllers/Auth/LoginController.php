<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        //-------------------Создаём запись в таблице user_infos + в users (Хмыров Н.А.)---------------------
        // $user_infos = new UserInfo();
        // $user_infos->last_name = "Хмыров";
        // $user_infos->first_name = "Никита";
        // $user_infos->middle_name = "Андреевич";
        // $user_infos->short_name = "Хмыров Н.А.";
        // $user_infos->position = "Старший оператор научной роты";
        // $user_infos->military_rank = "рядовой"; 
        // $user_infos->age = 24; 
        // $user_infos->save();

        // $user = new User;
        // $user->email = "969haki@mail.ru";
        // $user->password = Hash::make('London96');
        // $user->role_id = 1;
        // $user->object_id = 1;
        // $user->username = "NeKiToS"; 
        // $user->user_info_id=1;
        // $user->save();

        // $user = new User;
        // $user->email = "969haki@mail.ru";
        // $user->password = Hash::make('London969');
        // $user->role_id = 2;
        // $user->object_id = 1;
        // $user->username = "NeKiT"; 
        // $user->user_info_id=1;
        // $user->save();
    }

    public function username()
    {
        return 'username';
    }
}
