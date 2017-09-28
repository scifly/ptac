<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'ordersn'       => 'required|string|size:20|unique:orders,ordersn,' .
                $this->input('id') . ',id',
            'user_id'       => 'required|integer',
            'pay_user_id'   => 'required|integer',
            'combo_type_id' => 'required|integer',
            'transactionid' => 'required|string',
            'status'        => 'required|integer',
        ];
    }
}
