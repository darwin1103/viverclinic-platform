<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers { register as traitRegister; }

    public function register(\Illuminate\Http\Request $request)
    {
        $key = 'register_attempts:'.$request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            abort(429, 'Too Many Requests');
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        return $this->traitRegister($request);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(\Illuminate\Http\Request $request)
    {
        $branches = Branch::all();
        $documentTypes = \App\Models\DocumentType::where('status', true)->get();
        $genres = \App\Models\Gender::where('status', true)->get();
        $isLegacy = $request->query('legacy') == 1;
        return view('auth.register', compact('branches', 'documentTypes', 'genres', 'isLegacy'));
    }

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
        $this->middleware('throttle:10,1')->only('register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'branchId' => ['required'], // ***
            'referral_code' => ['nullable', 'string', 'max:255'],
            'is_legacy' => ['nullable', 'boolean'],
            'citizenship' => ['required', 'string', 'max:255'],
            'documentType' => ['required', 'exists:document_types,id'],
            'documentNumber' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'gender' => ['required', 'exists:genres,id'],
            'profession' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $referredById = null;
        $refCode = request()->query('ref') ?? ($data['ref'] ?? null);

        if (!empty($refCode)) {
            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referredById = $referrer->id;
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'referral_code' => User::generateReferralCode(),
            'is_legacy' => $data['is_legacy'] ?? false,
        ]);
        
        $user->citizenship = $data['citizenship'];
        $user->document_type_id = $data['documentType'];
        $user->document_number = $data['documentNumber'];
        $user->birthday = $data['birthday'];
        $user->gender_id = $data['gender'];
        $user->profession = $data['profession'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->save();

        $user->assignRole('PATIENT');

        $user->patientProfile()->create([
            'branch_id' => $data['branchId'],
        ]);

        // Procesar código de referido si fue proporcionado
        if (!empty($data['referral_code'])) {
            $referrer = User::where('referral_code', $data['referral_code'])->first();
            if ($referrer && $referrer->id !== $user->id) {
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                    'referred_name' => $user->name,
                    'referred_email' => $user->email,
                    'referred_phone' => $data['phone'] ?? null,
                    'status' => 'registered',
                ]);
            }
        }

        return $user;

    }

}
