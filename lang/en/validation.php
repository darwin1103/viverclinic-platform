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

    'accepted' => 'The condition must be accepted.',
    'accepted_if' => 'The field must be accepted when :other is :value.',
    'active_url' => 'The field is not a valid URL.',
    'after' => 'The field must be a date after :date.',
    'after_or_equal' => 'The field must be a date after or equal to :date.',
    'alpha' => 'The field must only contain letters.',
    'alpha_dash' => 'The field must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The field must only contain letters and numbers (No blank spaces).',
    'array' => 'The field must be an array.',
    'before' => 'The field must be a date before :date.',
    'before_or_equal' => 'The field must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The field must be between :min and :max.',
        'file' => 'The field must be between :min and :max kilobytes.',
        'string' => 'The field must be between :min and :max characters.',
        'array' => 'The field must have between :min and :max items.',
    ],
    'boolean' => 'The field field must be true or false.',
    'confirmed' => 'The confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The field is not a valid date.',
    'date_equals' => 'The field must be a date equal to :date.',
    'date_format' => 'The field does not match the format :format.',
    'declined' => 'The field must be declined.',
    'declined_if' => 'The field must be declined when :other is :value.',
    'different' => 'The field and :other must be different.',
    'digits' => 'The field must be :digits digits.',
    'digits_between' => 'The field must be between :min and :max digits.',
    'dimensions' => 'The field has invalid image dimensions.',
    'distinct' => 'The field field has a duplicate value.',
    'email' => 'The field must be a valid email address.',
    'ends_with' => 'The field must end with one of the following: :values.',
    'enum' => 'The selected field is invalid.',
    'exists' => 'The selected field is invalid.',
    'file' => 'The field must be a file.',
    'filled' => 'The field field must have a value.',
    'gt' => [
        'numeric' => 'The field must be greater than :value.',
        'file' => 'The field must be greater than :value kilobytes.',
        'string' => 'The field must be greater than :value characters.',
        'array' => 'The field must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The field must be greater than or equal to :value.',
        'file' => 'The field must be greater than or equal to :value kilobytes.',
        'string' => 'The field must be greater than or equal to :value characters.',
        'array' => 'The field must have :value items or more.',
    ],
    'image' => 'The field must be an image.',
    'in' => 'The selected field is invalid.',
    'in_array' => 'The field field does not exist in :other.',
    'integer' => 'The field must be an integer.',
    'ip' => 'The field must be a valid IP address.',
    'ipv4' => 'The field must be a valid IPv4 address.',
    'ipv6' => 'The field must be a valid IPv6 address.',
    'json' => 'The field must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The field must be less than :value.',
        'file' => 'The field must be less than :value kilobytes.',
        'string' => 'The field must be less than :value characters.',
        'array' => 'The field must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The field must be less than or equal to :value.',
        'file' => 'The field must be less than or equal to :value kilobytes.',
        'string' => 'The field must be less than or equal to :value characters.',
        'array' => 'The field must not have more than :value items.',
    ],
    'mac_address' => 'The field must be a valid MAC address.',
    'max' => [
        'numeric' => 'The field must not be greater than :max.',
        'file' => 'The field must not be greater than :max kilobytes.',
        'string' => 'The field must not be greater than :max characters.',
        'array' => 'The field must not have more than :max items.',
    ],
    'mimes' => 'The field must be a file of type: :values.',
    'mimetypes' => 'The field must be a file of type: :values.',
    'min' => [
        'numeric' => 'The field must be at least :min.',
        'file' => 'The field must be at least :min kilobytes.',
        'string' => 'The field must be at least :min characters.',
        'array' => 'The field must have at least :min items.',
    ],
    'multiple_of' => 'The field must be a multiple of :value.',
    'not_in' => 'The selected option is invalid.',
    'not_regex' => 'The field format is invalid.',
    'numeric' => 'The field must be a number.',
    'password' => 'The password is incorrect.',
    'present' => 'The field field must be present.',
    'prohibited' => 'The field field is prohibited.',
    'prohibited_if' => 'The field field is prohibited when :other is :value.',
    'prohibited_unless' => 'The field field is prohibited unless :other is in :values.',
    'prohibits' => 'The field field prohibits :other from being present.',
    'regex' => 'The field format is invalid.',
    'required' => 'The field is required.',
    'required_array_keys' => 'The field field must contain entries for: :values.',
    'required_if' => 'The field field is required when :other is :value.',
    'required_unless' => 'The field field is required unless :other is in :values.',
    'required_with' => 'The field field is required when :values is present.',
    'required_with_all' => 'The field field is required when :values are present.',
    'required_without' => 'The field field is required when :values is not present.',
    'required_without_all' => 'The field field is required when none of :values are present.',
    'same' => 'The field and :other must match.',
    'size' => [
        'numeric' => 'The field must be :size.',
        'file' => 'The field must be :size kilobytes.',
        'string' => 'The field must be :size characters.',
        'array' => 'The field must contain :size items.',
    ],
    'starts_with' => 'The field must start with one of the following: :values.',
    'string' => 'The field must be a string.',
    'timezone' => 'The field must be a valid timezone.',
    'unique' => 'The field has already been taken.',
    'uploaded' => 'The field failed to upload.',
    'url' => 'The field must be a valid URL.',
    'uuid' => 'The field must be a valid UUID.',

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

    'attributes' => [],

];
