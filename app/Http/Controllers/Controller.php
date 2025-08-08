<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function responseWithSuccess($data, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function responseWithError($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public function responseWithValidationErrors($errors, $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $errors,
        ], $status);
    }


    public function responseWithNotFound($message = 'Resource not found', $status = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public function responseWithUnauthorized($message = 'Unauthorized', $status = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    public function responseWithForbidden($message = 'Forbidden', $status = 403)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
