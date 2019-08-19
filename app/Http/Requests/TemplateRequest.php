<?php
namespace App\Http\Requests;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Models\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class TemplateRequest
 * @package App\Http\Requests
 */
class TemplateRequest extends FormRequest {
    
    use ModelTrait;
    
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
        
        $appIds = App::whereCategory(2)->pluck('id')->toArray();
        $industryIds = array_keys(Constant::INDUSTRY);
        
        return [
            'app_id'    => ['required', 'integer', Rule::in($appIds),],
            'primary'   => ['required', 'integer', Rule::in($industryIds), 'different:secondary'],
            'secondary' => ['required', 'integer', Rule::in($industryIds)],
        ];
        
    }
    
}
