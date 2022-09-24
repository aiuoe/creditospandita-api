<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Class LoginController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email'         => 'required|string|email|unique:users',
            'password'      => 'required|string',
        ]);

        if ($request->file()) {
            $imageName = time() . '.' . request()->file('file')->getClientOriginalExtension();
            $path = $request->file->storeAs('avatars', $imageName, 'uploads');
            $request->avatar = $path;
        } else {
            $request->avatar = "avatars/default-user.png";
        }

        $user = new User([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email'         => $request->email,
            'password'      => $request->password,
            'avatar'        => $request->avatar
        ]);

        //return response()->json(, 201); die;
        $user->save();




        return response()->json([
            'message' => 'Usuario creado correctamente!',
            'user' => $user
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $request->validate([
            'email'         => 'required',
            'password'      => 'required|string',
          
        ]);

        $credentials = $this->credentials($request);

        if (!Auth::attempt($credentials)) {
            
            return response()->json([
                'message' => 'Correo electronico y/o contraseña incorrecta. Vuelva a intentarlo o haz clic en olvido su contraseña',
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addDays(1);
        }

        $token->save();


        $expires_at = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();

        return response()->json([

            'access_token'  => $tokenResult->accessToken,

            'token_type'    => 'Bearer',
            'user'          => $user,
            'time'          => now(),
            'expires_at'    => $expires_at
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);
        $user = Auth::user();

        $user->password = $request->password;
        $user->save();

        return [
            'message' => 'Cambio de contraseña exitoso!',
        ];
    }

    public function refreshToken(Request $request)
    {
        $userId = (int) $request->input('user');
        $user = User::find($userId);
        $tokenResult = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'access_token' => $tokenResult,
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
        }
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    protected function credentials(Request $request)
    {
        //$field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_code';

        return [
            'email'    => $request->email,
            'password' => $request->password,
        ];
    }
}
