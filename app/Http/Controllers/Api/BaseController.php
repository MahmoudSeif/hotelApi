<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class BaseController extends Controller {
    protected function response($code,$message)
    {
        return response()->json(array_merge(['code' => $code, 'message' => $message]));
    }
}
