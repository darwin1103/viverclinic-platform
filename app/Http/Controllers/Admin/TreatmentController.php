<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;
use App\Models\BranchTreatment;

class TreatmentController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        // Iniciar la consulta base con eager loading para optimizar
        $query = Treatment::with('packages.branch');

        // 1. Filtro de búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        // 2. Filtro por estado (activo/inactivo)
        if ($request->filled('active')) {
            if ($request->input('active') === 'active') {
                $query->where('active', true);
            } elseif ($request->input('active') === 'inactive') {
                $query->where('active', false);
            }
            // Si es 'all' o cualquier otro valor, no se aplica filtro de estado
        }

        // 3. Filtro por sucursal
        if ($request->filled('branch_id') || session('selected_branch_id')) {
            $query->whereHas('branches', function ($branchQuery) use ($request) {
                $branchQuery->where('branches.id', $request->input('branch_id') ?? session('selected_branch_id'));
            });
        }

        // Obtener los resultados paginados
        // withQueryString() es crucial para que los links de paginación mantengan los filtros
        $treatments = $query->latest()->paginate(10)->withQueryString();

        // Las sucursales se necesitan para el selector del header y los filtros
        $branches = Branch::all();

        if ($request->has('branch_id')) {
            if ($request->filled('branch_id')) {
                session(['selected_branch_id' => $request->input('branch_id')]);
            } else {
                session()->forget('selected_branch_id');
            }
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.treatment.index', compact('treatments', 'branches', 'selectedBranchID'));
    }

     public function create()
    {
        $branches = Branch::all();
        return view('admin.treatment.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $messages = [
            // --- Reglas de Nivel Superior ---
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser una cadena de texto.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',
            'name.unique'     => 'Este nombre de tratamiento ya existe.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string'   => 'La descripción debe ser una cadena de texto.',

            'sessions.required' => 'El número de sesiones es obligatorio.',
            'sessions.integer'  => 'El número de sesiones debe ser un número entero.',
            'sessions.min'      => 'El número de sesiones mínimo es :min.',

            'days_between_sessions.required' => 'Los días entre sesiones son obligatorios.',
            'days_between_sessions.integer'  => 'Los días entre sesiones deben ser un número entero.',
            'days_between_sessions.min'      => 'Los días entre sesiones mínimos son :min.',

            'active.boolean'             => 'El campo activo debe ser verdadero o falso.',
            'needs_report_shots.boolean' => 'El campo de fotos de reporte debe ser verdadero o falso.',

            'main_image.image' => 'El archivo debe ser una imagen.',
            'main_image.mimes' => 'La imagen principal debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'main_image.max'   => 'La imagen principal no debe pesar más de :max kilobytes (2 MB).',

            'price_additional_zone.required' => 'El precio por zona adicional es obligatorio.',
            'price_additional_zone.numeric'  => 'El precio por zona adicional debe ser un número.',
            'price_additional_zone.min'      => 'El precio por zona adicional no puede ser menor a :min.',

            'price_additional_mini_zone.required' => 'El precio por mini zona adicional es obligatorio.',
            'price_additional_mini_zone.numeric'  => 'El precio por mini zona adicional debe ser un número.',
            'price_additional_mini_zone.min'      => 'El precio por mini zona adicional no puede ser menor a :min.',

            'branches.array'       => 'El campo de sucursales debe ser un arreglo.',
            'terms_conditions.string' => 'Los términos y condiciones deben ser texto.',

            // --- Reglas Anidadas para Paquetes (branches.*.packages.*) ---
            'branches.*.packages.array' => 'Los paquetes de cada sucursal deben ser un arreglo.',

            'branches.*.packages.*.name.required' => 'El nombre del paquete es obligatorio.',
            'branches.*.packages.*.name.string'   => 'El nombre del paquete debe ser una cadena de texto.',
            'branches.*.packages.*.name.max'      => 'El nombre del paquete no debe exceder los :max caracteres.',

            'branches.*.packages.*.price.required' => 'El precio del paquete es obligatorio.',
            'branches.*.packages.*.price.numeric'  => 'El precio del paquete debe ser un número.',
            'branches.*.packages.*.price.min'      => 'El precio del paquete no puede ser menor a :min.',

            'branches.*.packages.*.big_zones.required' => 'Las zonas grandes son obligatorias.',
            'branches.*.packages.*.big_zones.integer'  => 'Las zonas grandes deben ser un número entero.',
            'branches.*.packages.*.big_zones.min'      => 'El número de zonas grandes no puede ser menor a :min.',

            'branches.*.packages.*.mini_zones.required' => 'Las mini zonas son obligatorias.',
            'branches.*.packages.*.mini_zones.integer'  => 'Las mini zonas deben ser un número entero.',
            'branches.*.packages.*.mini_zones.min'      => 'El número de mini zonas no puede ser menor a :min.',

            'branches.*.packages.*.sessions.required_if' => 'El número de sesiones del paquete es obligatorio si se activa la personalización.',
            'branches.*.packages.*.sessions.integer' => 'El número de sesiones del paquete debe ser un número entero.',
            'branches.*.packages.*.sessions.min' => 'El número de sesiones del paquete mínimo es :min.',
        ];

        $attributes = [
            'name'                         => 'Nombre del Tratamiento',
            'description'                  => 'Descripción',
            'sessions'                     => 'Sesiones',
            'days_between_sessions'        => 'Días entre Sesiones',
            'active'                       => 'Activo',
            'needs_report_shots'           => 'Necesita Fotos de Reporte',
            'main_image'                   => 'Imagen Principal',
            'price_additional_zone'        => 'Precio Zona Adicional',
            'price_additional_mini_zone'   => 'Precio Mini Zona Adicional',
            'branches'                     => 'Sucursales',
            'terms_conditions'             => 'Términos y Condiciones',

            // Atributos anidados para mejor referencia si se usa el archivo de idioma
            'branches.*.packages.*.name'   => 'Nombre del Paquete',
            'branches.*.packages.*.price'  => 'Precio del Paquete',
            'branches.*.packages.*.big_zones'  => 'Zonas Grandes',
            'branches.*.packages.*.mini_zones' => 'Mini Zonas',
            'branches.*.packages.*.custom_sessions' => 'Personalizar Sesiones del Paquete',
            'branches.*.packages.*.sessions' => 'Sesiones del Paquete',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:treatments,name',
            'description' => 'required|string',
            'sessions' => 'required|integer|min:1',
            'days_between_sessions' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'needs_report_shots' => 'sometimes|boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price_additional_zone' => 'required|numeric|min:0',
            'price_additional_mini_zone' => 'required|numeric|min:0',
            'branches' => 'nullable|array',
            'branches.*.packages.*.installments' => [
                'nullable',
                'array',
                // Validación personalizada: count(installments) <= sessions
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/branches\.(\d+)\.packages\.([^\.]+)\.installments/', $attribute, $matches);
                    if (count($matches) === 3) {
                        $branchId = $matches[1];
                        $pkgIndex = $matches[2];
                        
                        $packageInput = $request->input("branches.{$branchId}.packages.{$pkgIndex}");
                        $hasCustomSessions = isset($packageInput['custom_sessions']) && $packageInput['custom_sessions'] == '1';
                        $maxSessions = $hasCustomSessions && isset($packageInput['sessions']) 
                            ? (int)$packageInput['sessions'] 
                            : (int)$request->input('sessions');
                            
                        if (count($value) > $maxSessions) {
                            $fail('El número de cuotas no puede ser mayor que la cantidad de sesiones (' . $maxSessions . ').');
                        }
                    } else {
                        if (count($value) > $request->input('sessions')) {
                            $fail('El número de cuotas no puede ser mayor que la cantidad de sesiones (' . $request->input('sessions') . ').');
                        }
                    }
                },
            ],
            'branches.*.packages' => 'nullable|array',
            'branches.*.packages.*.name' => 'required|string|max:255',
            'branches.*.packages.*.price' => 'required|numeric|min:0',
            'branches.*.packages.*.big_zones' => 'required|integer|min:0',
            'branches.*.packages.*.mini_zones' => 'required|integer|min:0',
            'branches.*.packages.*.custom_sessions' => 'nullable|boolean',
            'branches.*.packages.*.sessions' => 'required_if:branches.*.packages.*.custom_sessions,1|nullable|integer|min:1',
            'branches.*.packages.*.installment_conditions' => 'nullable|string|max:500',
            'terms_conditions' => 'nullable|string',
        ], $messages, $attributes);

        $treatmentData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'sessions' => $validated['sessions'],
            'days_between_sessions' => $validated['days_between_sessions'],
            'price_additional_zone' => $validated['price_additional_zone'],
            'price_additional_mini_zone' => $validated['price_additional_mini_zone'],
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'active' => $request->has('active'),
            'needs_report_shots' => $request->has('needs_report_shots'),
        ];

        if ($request->hasFile('main_image')) {
            $treatmentData['main_image'] = $this->uploadFile($request->file('main_image'), 'treatments');
        }

        $treatment = Treatment::create($treatmentData);

        // Guardar Paquetes y Cuotas
        if (isset($validated['branches'])) {
            foreach ($validated['branches'] as $branchId => $branchData) {
                if (isset($branchData['packages'])) {
                    foreach ($branchData['packages'] as $packageData) {

                        $allowInstallments = isset($packageData['allow_installments']) && $packageData['allow_installments'] == '1';
                        $customSessions = isset($packageData['custom_sessions']) && $packageData['custom_sessions'] == '1';

                        $package = $treatment->packages()->create([
                            'branch_id' => $branchId,
                            'name' => $packageData['name'],
                            'price' => $packageData['price'],
                            'big_zones' => $packageData['big_zones'],
                            'mini_zones' => $packageData['mini_zones'],
                            'allow_installments' => $allowInstallments,
                            'installment_conditions' => !empty($packageData['installment_conditions']) ? $packageData['installment_conditions'] : 'Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión',
                            'custom_sessions' => $customSessions,
                            'sessions' => $customSessions ? $packageData['sessions'] : null,
                        ]);

                        // Guardar Cuotas si aplica
                        if ($allowInstallments && isset($packageData['installments'])) {
                            $i = 1;
                            foreach ($packageData['installments'] as $instData) {
                                $package->installments()->create([
                                    'installment_number' => $i++,
                                    'price' => $instData['price']
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento creado con éxito.');
    }

    public function show(Treatment $treatment)
    {
        //
    }

    public function edit(Treatment $treatment)
    {
        $branches = Branch::all();
        $treatment->load('packages.branch');
        return view('admin.treatment.edit', compact('treatment', 'branches'));
    }

    public function update(Request $request, Treatment $treatment)
    {
        $messages = [
            // --- Reglas de Nivel Superior ---
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser una cadena de texto.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',
            'name.unique'     => 'Este nombre de tratamiento ya existe.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string'   => 'La descripción debe ser una cadena de texto.',

            'sessions.required' => 'El número de sesiones es obligatorio.',
            'sessions.integer'  => 'El número de sesiones debe ser un número entero.',
            'sessions.min'      => 'El número de sesiones mínimo es :min.',

            'days_between_sessions.required' => 'Los días entre sesiones son obligatorios.',
            'days_between_sessions.integer'  => 'Los días entre sesiones deben ser un número entero.',
            'days_between_sessions.min'      => 'Los días entre sesiones mínimos son :min.',

            'active.boolean'             => 'El campo activo debe ser verdadero o falso.',
            'needs_report_shots.boolean' => 'El campo de fotos de reporte debe ser verdadero o falso.',

            'main_image.image' => 'El archivo debe ser una imagen.',
            'main_image.mimes' => 'La imagen principal debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'main_image.max'   => 'La imagen principal no debe pesar más de :max kilobytes (2 MB).',

            'price_additional_zone.required' => 'El precio por zona adicional es obligatorio.',
            'price_additional_zone.numeric'  => 'El precio por zona adicional debe ser un número.',
            'price_additional_zone.min'      => 'El precio por zona adicional no puede ser menor a :min.',

            'price_additional_mini_zone.required' => 'El precio por mini zona adicional es obligatorio.',
            'price_additional_mini_zone.numeric'  => 'El precio por mini zona adicional debe ser un número.',
            'price_additional_mini_zone.min'      => 'El precio por mini zona adicional no puede ser menor a :min.',

            'branches.array'       => 'El campo de sucursales debe ser un arreglo.',
            'terms_conditions.string' => 'Los términos y condiciones deben ser texto.',

            // --- Reglas Anidadas para Paquetes (branches.*.packages.*) ---
            'branches.*.packages.array' => 'Los paquetes de cada sucursal deben ser un arreglo.',

            'branches.*.packages.*.name.required' => 'El nombre del paquete es obligatorio.',
            'branches.*.packages.*.name.string'   => 'El nombre del paquete debe ser una cadena de texto.',
            'branches.*.packages.*.name.max'      => 'El nombre del paquete no debe exceder los :max caracteres.',

            'branches.*.packages.*.price.required' => 'El precio del paquete es obligatorio.',
            'branches.*.packages.*.price.numeric'  => 'El precio del paquete debe ser un número.',
            'branches.*.packages.*.price.min'      => 'El precio del paquete no puede ser menor a :min.',

            'branches.*.packages.*.big_zones.required' => 'Las zonas grandes son obligatorias.',
            'branches.*.packages.*.big_zones.integer'  => 'Las zonas grandes deben ser un número entero.',
            'branches.*.packages.*.big_zones.min'      => 'El número de zonas grandes no puede ser menor a :min.',

            'branches.*.packages.*.mini_zones.required' => 'Las mini zonas son obligatorias.',
            'branches.*.packages.*.mini_zones.integer'  => 'Las mini zonas deben ser un número entero.',
            'branches.*.packages.*.mini_zones.min'      => 'El número de mini zonas no puede ser menor a :min.',

            'branches.*.packages.*.sessions.required_if' => 'El número de sesiones del paquete es obligatorio si se activa la personalización.',
            'branches.*.packages.*.sessions.integer' => 'El número de sesiones del paquete debe ser un número entero.',
            'branches.*.packages.*.sessions.min' => 'El número de sesiones del paquete mínimo es :min.',
        ];

        $attributes = [
            'name'                         => 'Nombre del Tratamiento',
            'description'                  => 'Descripción',
            'sessions'                     => 'Sesiones',
            'days_between_sessions'        => 'Días entre Sesiones',
            'active'                       => 'Activo',
            'needs_report_shots'           => 'Necesita Fotos de Reporte',
            'main_image'                   => 'Imagen Principal',
            'price_additional_zone'        => 'Precio Zona Adicional',
            'price_additional_mini_zone'   => 'Precio Mini Zona Adicional',
            'branches'                     => 'Sucursales',
            'terms_conditions'             => 'Términos y Condiciones',

            // Atributos anidados para mejor referencia si se usa el archivo de idioma
            'branches.*.packages.*.name'   => 'Nombre del Paquete',
            'branches.*.packages.*.price'  => 'Precio del Paquete',
            'branches.*.packages.*.big_zones'  => 'Zonas Grandes',
            'branches.*.packages.*.mini_zones' => 'Mini Zonas',
            'branches.*.packages.*.custom_sessions' => 'Personalizar Sesiones del Paquete',
            'branches.*.packages.*.sessions' => 'Sesiones del Paquete',
        ];

        $validated = $request->validate([
            // CORRECCIÓN AQUÍ: Concatenamos el ID del tratamiento al final de la regla unique
            'name' => 'required|string|max:255|unique:treatments,name,' . $treatment->id,

            'description' => 'required|string',
            'sessions' => 'required|integer|min:1',
            'days_between_sessions' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'needs_report_shots' => 'sometimes|boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price_additional_zone' => 'required|numeric|min:0',
            'price_additional_mini_zone' => 'required|numeric|min:0',
            'branches' => 'nullable|array',
            'branches.*.packages' => 'nullable|array',
            'branches.*.packages.*.name' => 'required|string|max:255',
            'branches.*.packages.*.price' => 'required|numeric|min:0',
            'branches.*.packages.*.big_zones' => 'required|integer|min:0',
            'branches.*.packages.*.mini_zones' => 'required|integer|min:0',
            'branches.*.packages.*.custom_sessions' => 'nullable|boolean',
            'branches.*.packages.*.sessions' => 'required_if:branches.*.packages.*.custom_sessions,1|nullable|integer|min:1',
            'branches.*.packages.*.allow_installments' => 'nullable|boolean',
            'branches.*.packages.*.installment_conditions' => 'nullable|string|max:500',
            'branches.*.packages.*.installments' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/branches\.(\d+)\.packages\.([^\.]+)\.installments/', $attribute, $matches);
                    if (count($matches) === 3) {
                        $branchId = $matches[1];
                        $pkgIndex = $matches[2];
                        
                        $packageInput = $request->input("branches.{$branchId}.packages.{$pkgIndex}");
                        $hasCustomSessions = isset($packageInput['custom_sessions']) && $packageInput['custom_sessions'] == '1';
                        $maxSessions = $hasCustomSessions && isset($packageInput['sessions']) 
                            ? (int)$packageInput['sessions'] 
                            : (int)$request->input('sessions');
                            
                        if (count($value) > $maxSessions) {
                            $fail('El número de cuotas no puede ser mayor que la cantidad de sesiones (' . $maxSessions . ').');
                        }
                    } else {
                        if (count($value) > $request->input('sessions')) {
                            $fail('El número de cuotas no puede ser mayor que la cantidad de sesiones (' . $request->input('sessions') . ').');
                        }
                    }
                },
            ],
            'branches.*.packages.*.installments.*.price' => 'required_with:branches.*.packages.*.installments|numeric|min:0',

            'terms_conditions' => 'nullable|string',
        ], $messages, $attributes);

        $treatmentData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'sessions' => $validated['sessions'],
            'days_between_sessions' => $validated['days_between_sessions'],
            'price_additional_zone' => $validated['price_additional_zone'],
            'price_additional_mini_zone' => $validated['price_additional_mini_zone'],
            'terms_conditions' => $validated['terms_conditions'] ?? null,
            'active' => $request->has('active'),
            'needs_report_shots' => $request->has('needs_report_shots'),
        ];

        if ($request->hasFile('main_image')) {
            $treatmentData['main_image'] = $this->uploadFile($request->file('main_image'), 'treatments', $treatment->main_image);
        }
        $treatment->update($treatmentData);

        // Actualizar Paquetes (Estrategia: Borrar y recrear para limpiar asociaciones complejas)
        $treatment->packages()->each(function($pkg) {
            $pkg->installments()->delete(); // Borrar cuotas viejas
            $pkg->delete(); // Borrar paquete
        });

        if (isset($validated['branches'])) {
            foreach ($validated['branches'] as $branchId => $branchData) {
                if (isset($branchData['packages'])) {
                    foreach ($branchData['packages'] as $packageData) {

                        $allowInstallments = isset($packageData['allow_installments']) && $packageData['allow_installments'] == '1';
                        $customSessions = isset($packageData['custom_sessions']) && $packageData['custom_sessions'] == '1';

                        $package = $treatment->packages()->create([
                            'branch_id' => $branchId,
                            'name' => $packageData['name'],
                            'price' => $packageData['price'],
                            'big_zones' => $packageData['big_zones'],
                            'mini_zones' => $packageData['mini_zones'],
                            'allow_installments' => $allowInstallments,
                            'installment_conditions' => !empty($packageData['installment_conditions']) ? $packageData['installment_conditions'] : 'Cancela el 50% del tratamiento para comenzar y el otro 50% en la tercera sesión',
                            'custom_sessions' => $customSessions,
                            'sessions' => $customSessions ? $packageData['sessions'] : null,
                        ]);

                        if ($allowInstallments && isset($packageData['installments'])) {
                            $i = 1;
                            foreach ($packageData['installments'] as $instData) {
                                $package->installments()->create([
                                    'installment_number' => $i++,
                                    'price' => $instData['price']
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento actualizado con éxito.');
    }

    public function destroy(Treatment $treatment)
    {
        try {
            if ($treatment->main_image) {
                Storage::disk('public')->delete($treatment->main_image);
            }
            $treatment->delete();
            return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento eliminado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar este registro porque tiene datos dependientes asociados.']);
        }
    }
}
