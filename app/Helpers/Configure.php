<?php
namespace App\Helpers;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use ReflectionException;
use Throwable;

/**
 * Class Configure
 * @package App\Helpers
 */
class Configure {
    
    use ModelTrait;
    
    const TPL = <<<HTML
        <tr>
            <td>%s</td>
            <td class="text-center">%s</td>
            <td>%s</td>
            <td class="text-center">%s</td>
            <td class="text-right">%s</td>
        </tr>
    HTML;
    
    function __construct() { }
    
    /**
     * @param $class
     * @return string
     * @throws ReflectionException
     */
    function html($class = null) {
        
        $records = $this->model($class ?? Request::input('paramId'))->all();
        $list = '';
        foreach ($records as $record) {
            $list .= sprintf(
                self::TPL,
                $record->{'id'},
                $record->{'name'},
                $record->{'remark'},
                $this->humanDate($record->{'created_at'}),
                $record->{'enabled'} ? '已启用' : '已禁用'
            );
        }
        
        return $list;
        
    }
    
    /**
     * @return array
     * @throws ReflectionException
     */
    function compose() {
        
        $params = Arr::pluck(Constant::SYSTEM_PARAMS, 'name', 'id');
        
        return [
            'params' => $params,
            'list'   => $this->html(key($params)),
        ];
        
    }
    
    /**
     * 初始化系统参数
     *
     * @return bool
     * @throws Throwable
     */
    function init() {
        
        try {
            DB::transaction(function () { });
            $params = Request::has('paramId')
                ? [Request::input('paramId')]
                : Arr::pluck(Constant::SYSTEM_PARAMS, 'id');
            foreach ($params as $param) {
                $model = $this->model($param);
                $model->truncate();
                $key = array_search($param, array_column(
                    Constant::SYSTEM_PARAMS, 'id'
                ));
                $data = Constant::SYSTEM_PARAMS[$key]['data'];
                foreach ($data as $datum) {
                    $records[] = array_combine(
                        ['name', 'color', 'icon', 'remark', 'created_at', 'updated_at', 'enabled'],
                        array_merge($datum, [
                            now()->toDateTimeString(),
                            now()->toDateTimeString(),
                            Constant::ENABLED,
                        ])
                    );
                }
                $model->insert($records ?? []);
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}