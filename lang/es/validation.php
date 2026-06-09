<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El campo debe ser aceptado.',
    'accepted_if'          => 'El campo debe ser aceptado cuando :other es :value.',
    'active_url'           => 'El campo no es una URL válida.',
    'after'                => 'El campo debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El campo solo puede contener letras.',
    'alpha_dash'           => 'El campo solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num'            => 'El campo solo puede contener letras y números (Sin espacios en blanco).',
    'array'                => 'El campo debe ser un arreglo.',
    'before'               => 'El campo debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => 'El campo debe estar entre :min y :max.',
        'file'    => 'El archivo debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo debe tener entre :min y :max caracteres.',
        'array'   => 'El campo debe tener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación no coincide.',
    'current_password'     => 'La contraseña es incorrecta.',
    'date'                 => 'El campo no es una fecha válida.',
    'date_equals'          => 'El campo debe ser una fecha igual a :date.',
    'date_format'          => 'El campo no corresponde al formato :format.',
    'declined'             => 'El campo debe ser rechazado.',
    'declined_if'          => 'El campo debe ser rechazado cuando :other es :value.',
    'different'            => 'El campo y :other deben ser diferentes.',
    'digits'               => 'El campo debe tener :digits dígitos.',
    'digits_between'       => 'El campo debe tener entre :min y :max dígitos.',
    'dimensions'           => 'El campo tiene dimensiones de imagen inválidas.',
    'distinct'             => 'El campo contiene un valor duplicado.',
    'email'                => 'El campo debe ser una dirección de correo electrónico válida.',
    'ends_with'            => 'El campo debe finalizar con uno de los siguientes valores: :values.',
    'enum'                 => 'La opción seleccionada es inválida.',
    'exists'               => 'La opción seleccionada es inválida.',
    'file'                 => 'El campo debe ser un archivo.',
    'filled'               => 'El campo es obligatorio.',
    'gt'                   => [
        'numeric' => 'El campo debe ser mayor que :value.',
        'file'    => 'El archivo debe pesar más de :value kilobytes.',
        'string'  => 'El campo debe tener más de :value caracteres.',
        'array'   => 'El campo debe tener más de :value elementos.',
    ],
    'gte'                  => [
        'numeric' => 'El campo debe ser mayor o igual que :value.',
        'file'    => 'El archivo debe pesar :value kilobytes o más.',
        'string'  => 'El campo debe tener :value caracteres o más.',
        'array'   => 'El campo debe tener :value elementos o más.',
    ],
    'image'                => 'El campo debe ser una imagen.',
    'in'                   => 'La opción seleccionada es inválida.',
    'in_array'             => 'El campo no existe en :other.',
    'integer'              => 'El campo debe ser un número entero.',
    'ip'                   => 'El campo debe ser una dirección IP válida.',
    'ipv4'                 => 'El campo debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El campo debe ser una dirección IPv6 válida.',
    'json'                 => 'El campo debe ser una cadena JSON válida.',
    'lt'                   => [
        'numeric' => 'El campo debe ser menor que :value.',
        'file'    => 'El archivo debe pesar menos de :value kilobytes.',
        'string'  => 'El campo debe tener menos de :value caracteres.',
        'array'   => 'El campo debe tener menos de :value elementos.',
    ],
    'lte'                  => [
        'numeric' => 'El campo debe ser menor o igual que :value.',
        'file'    => 'El archivo debe pesar :value kilobytes o menos.',
        'string'  => 'El campo debe tener :value caracteres o menos.',
        'array'   => 'El campo no debe tener más de :value elementos.',
    ],
    'mac_address'          => 'El campo debe ser una dirección MAC válida.',
    'max'                  => [
        'numeric' => 'El campo no debe ser mayor que :max.',
        'file'    => 'El archivo no debe pesar más de :max kilobytes.',
        'string'  => 'El campo no debe tener más de :max caracteres.',
        'array'   => 'El campo no debe tener más de :max elementos.',
    ],
    'mimes'                => 'El campo debe ser un archivo de tipo: :values.',
    'mimetypes'            => 'El campo debe ser un archivo de tipo: :values.',
    'min'                  => [
        'numeric' => 'El campo debe ser al menos :min.',
        'file'    => 'El archivo debe pesar al menos :min kilobytes.',
        'string'  => 'El campo debe tener al menos :min caracteres.',
        'array'   => 'El campo debe tener al menos :min elementos.',
    ],
    'multiple_of'          => 'El campo debe ser múltiplo de :value.',
    'not_in'               => 'La opción seleccionada es inválida.',
    'not_regex'            => 'El formato del campo es inválido.',
    'numeric'              => 'El campo debe ser un número.',
    'password'             => 'La contraseña es incorrecta.',
    'present'              => 'El campo debe estar presente.',
    'prohibited'           => 'El campo está prohibido.',
    'prohibited_if'        => 'El campo está prohibido cuando :other es :value.',
    'prohibited_unless'    => 'El campo está prohibido a menos que :other sea :values.',
    'prohibits'            => 'El campo prohíbe que :other esté presente.',
    'regex'                => 'El formato del campo es inválido.',
    'required'             => 'El campo es requerido.',
    'required_array_keys'  => 'El campo debe contener entradas para: :values.',
    'required_if'          => 'El campo es requerido cuando :other es :value.',
    'required_unless'      => 'El campo es requerido a menos que :other esté en :values.',
    'required_with'        => 'El campo es requerido cuando :values está presente.',
    'required_with_all'    => 'El campo es requerido cuando :values están presentes.',
    'required_without'     => 'El campo es requerido cuando :values no está presente.',
    'required_without_all' => 'El campo es requerido cuando ninguno de :values están presentes.',
    'same'                 => 'El campo y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El campo debe ser :size.',
        'file'    => 'El archivo debe pesar :size kilobytes.',
        'string'  => 'El campo debe tener :size caracteres.',
        'array'   => 'El campo debe contener :size elementos.',
    ],
    'starts_with'          => 'El campo debe comenzar con uno de los siguientes valores: :values.',
    'string'               => 'El campo debe ser una cadena de texto.',
    'timezone'             => 'El campo debe ser una zona horaria válida.',
    'unique'               => 'El valor ingresado ya está en uso.',
    'uploaded'             => 'El campo falló al subir.',
    'url'                  => 'El campo debe ser una URL válida.',
    'uuid'                 => 'El campo debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'name' => 'nombre',
        'phone' => 'teléfono',
    ],

];
