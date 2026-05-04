<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentSignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(ContractedTreatment $contractedTreatment)
    {
        if ($contractedTreatment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($contractedTreatment->terms_acepted) {
            return redirect()->route('dashboard');
        }

        return view('client.consent-signature.create', compact('contractedTreatment'));
    }

    public function store(Request $request, ContractedTreatment $contractedTreatment)
    {
        if ($contractedTreatment->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'terms_accepted' => 'required|accepted',
            'is_pregnant' => 'required|in:0',
        ], [
            'terms_accepted.required' => 'Debes aceptar los términos y condiciones.',
            'terms_accepted.accepted' => 'Debes aceptar los términos y condiciones.',
            'is_pregnant.required' => 'Debes declarar si estás en estado de embarazo.',
            'is_pregnant.in' => 'Lamentablemente no podemos realizar este tratamiento si te encuentras en estado de embarazo.',
        ]);

        $contractedTreatment->terms_acepted = true;
        $contractedTreatment->is_pregnant = false;
        $contractedTreatment->save();

        return redirect()->route('dashboard')->with('success', 'Consentimiento firmado correctamente. Ya puedes agendar tus citas.');
    }
}
