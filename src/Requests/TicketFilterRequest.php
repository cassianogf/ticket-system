<?php

namespace Kordy\Ticketit\Requests;

use App\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

class TicketFilterRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function response(array $errors)
    {
        return new JsonResponse($errors, 422);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }


}
