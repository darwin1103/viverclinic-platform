<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $key = 'login_attempts:' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            abort(429, 'Too Many Requests');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            if (!$user->active) {
                Auth::logout();
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([$this->username() => 'Su cuenta está desactivada. Por favor, póngase en contacto con el administrador para más información.']);
            }
            \Illuminate\Support\Facades\RateLimiter::clear($key);
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * The user has been authenticated.
     * Auto-set the branch in session for ADMIN users.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('ADMIN')) {
            $branchId = $user->adminsBranches()->first()?->id;
            if ($branchId) {
                session(['selected_branch_id' => $branchId]);
            }
        } elseif ($user->hasRole('SALES')) {
            $branchId = $user->salesProfile?->branch_id;
            if ($branchId) {
                session(['selected_branch_id' => $branchId]);
            }
        } elseif ($user->hasRole('EMPLOYEE')) {
            $branchId = $user->staffProfile?->branch_id;
            if ($branchId) {
                session(['selected_branch_id' => $branchId]);
            }
        } elseif ($user->hasRole('PATIENT')) {
            session(['show_welcome_popup' => true]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    
}
