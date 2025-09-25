<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    const SUPER_ADMIN_ROLE_ID = 1;
    const ERROR_GENERAL_MSG = 'Something went wrong, please try again, if the problem persists, please report it to administrator';
}
