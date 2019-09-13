<?php
namespace App\Http\Requests;

use App\Models\Building;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RoomRequest
 * @package App\Http\Requests
 */
class RoomRequest extends FormRequest {
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }
    
    /**
     * @return array
     */
    public function rules() {
        
        return [
            'name'         => 'required|string|max:255|unique:grades,name,' .
                $this->input('id') . ',id,' .
                'building_id,' . $this->input('building_id') .
                'room_type_id,' . $this->input('room_type_id'),
            'building_id'  => 'required|integer',
            'room_type_id' => 'required|integer',
            'floor'        => 'required|integer',
            'volume'       => 'nullable|integer',
            'remark'       => 'nullable|string|max:255',
            'enabled'      => 'required|boolean',
        ];
        
    }
    
    protected function prepareForValidation() {
        
        $input = $this->all();
        $floors = Building::find($input['building_id'])->floors;
        $input['floor'] <= $floors ?: $input['floor'] = $floors;
        $this->replace($input);
        
    }
    
}
