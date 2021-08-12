<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class CustomFormRequest extends FormRequest
{
    /**
     * Custom Failed Response
     *
     * Overrides the Illuminate\Foundation\Http\FormRequest
     * response function to stop it from auto redirecting
     * and applies a API custom response format.
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors) {

        // Put whatever response you want here.
        return new JsonResponse([
            'status' => '422',
            'errors' => $errors,
        ], 422);
    }
}
