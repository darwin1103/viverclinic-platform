<?php

namespace App\Http\Requests\Client;

use App\Models\Treatment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTreatmentRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Cambia a true para permitir la request. Puedes añadir lógica
        // de autorización aquí, por ejemplo, si el usuario está autenticado.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $user = Auth::user();
        $branch = $user->patientProfile->branch;

        $treatment_id = $this->input('treatment_id', null);

        $treatment = Treatment::find($treatment_id);

        $validPackageIds = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->pluck('id') // <-- El método clave: selecciona solo la columna 'id'
            ->all();      // <-- Convierte la colección de IDs en un array simple

        $validAdditionalIds = ['mini', 'big'];
// dd($this->input('package', null));
        return [
            // --- VALIDACIÓN DE package Y additional ---

            'treatment_id' => ['required', 'exists:treatments,id'],

            'package' => [
                'required_without:additional',
                'nullable',
                'array',
                // Validar las CLAVES del array de package
                function ($attribute, $value, $fail) use ($validPackageIds) {
                    // $value es el array, por ej: [1 => "1", 3 => "2"]
                    // array_keys($value) nos da las claves: [1, 3]
                    foreach (array_keys($value) as $packageId) {
                        if (!in_array($packageId, $validPackageIds)) {
                            // Si una clave no está en la lista de IDs válidos, falla la validación.
                            $fail("El paquete con el ID {$packageId} es inválido.");
                        }
                    }
                }
            ],

            // Usar 'numeric' en lugar de 'integer' para los VALORES
            'package.*' => ['numeric', 'min:0'],

            'termsConditions' => ['required', 'accepted'],
            'notPregnant' => ['nullable'],

            'additional' => [
                'required_without:package',
                'nullable',
                'array',
                 // Aplicar la misma lógica de validación de CLAVES aquí
                function ($attribute, $value, $fail) use ($validAdditionalIds) {
                    foreach (array_keys($value) as $additionalId) {
                        if (!in_array($additionalId, $validAdditionalIds)) {
                            $fail("La zona adicional con el ID {$additionalId} es inválida.");
                        }
                    }
                }
            ],

            // Usar 'numeric' también aquí para los VALORES
            'additional.*' => ['numeric', 'min:0'],

            // --- VALIDACIÓN DE ZONAS SELECCIONADAS ---

            // 'selected_zones' debe ser un array si existe.
            'selected_zones' => ['nullable', 'array'],

            // Si se envían zonas 'big', deben ser un array.
            'selected_zones.big' => ['nullable', 'array'],
            'selected_zones.big.*' => ['string', 'max:100'], // Cada zona es un string.

            // Si se envían zonas 'mini', deben ser un array.
            'selected_zones.mini' => ['nullable', 'array'],
            'selected_zones.mini.*' => ['string', 'max:100'], // Cada zona es un string.

            // --- VALIDACIÓN DE ZONAS PERSONALIZADAS ---

            // Son opcionales (nullable), pero si se envían, deben ser un string.
            'another_big_zone' => ['nullable', 'string', 'max:255'],
            'another_mini_zone' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            $user = Auth::user();
            $branch = $user->patientProfile->branch;

            $treatment_id = $this->input('treatment_id', null);

            $treatment = Treatment::find($treatment_id);

            $packages = $treatment->packages()
                ->where('branch_id', $branch->id)
                ->select('id', 'name', 'price', 'big_zones', 'mini_zones')
                ->get()
                ->keyBy('id');

            // 1. Calcular total de zonas permitidas según la compra
            $allowedLargeZones = 0;
            $allowedMiniZones = 0;

            foreach ($this->input('package', []) as $id => $cantidad) {
                $package = $packages->get($id);
                if ($package) {
                    $allowedLargeZones += $package->big_zones * $cantidad;
                    $allowedMiniZones += $package->mini_zones * $cantidad;
                }
            }

            foreach ($this->input('additional', []) as $id => $cantidad) {
                if ($id === 'big') {
                    $allowedLargeZones += 1 * $cantidad;
                } else {
                    $allowedMiniZones += 1 * $cantidad;
                }
            }

            // 2. Contar total de zonas seleccionadas
            $selectedLargeZones = count($this->input('selected_zones.big', []));
            if ($this->filled('another_big_zone')) {
                $selectedLargeZones++;
            }

            $selectedMiniZones = count($this->input('selected_zones.mini', []));
            if ($this->filled('another_mini_zone')) {
                $selectedMiniZones++;
            }

            // 3. Comparar y añadir error si no coinciden
            if ($selectedLargeZones !== $allowedLargeZones || $selectedMiniZones !== $allowedMiniZones) {
                $validator->errors()->add(
                    'selected_zones',
                    'El número de zonas seleccionadas no coincide con las zonas adquiridas en tu plan. Por favor, verifica tu selección.'
                );
            }
        });
    }
}
