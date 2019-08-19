<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\TemplateRequest;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Class Template - 公众号消息模板
 *
 * @package App\Models
 * @property int $id
 * @property int $app_id 所属公众号应用id
 * @property string $title 模板标题
 * @property string $templateid 模板ID
 * @property string $primary_industry 模板所属行业的一级行业
 * @property string $deputy_industry 模板所属行业的二级行业
 * @property string $content 模板内容
 * @property string $example 模板示例
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read App $app
 * @method static Builder|Template newModelQuery()
 * @method static Builder|Template newQuery()
 * @method static Builder|Template query()
 * @method static Builder|Template whereAppId($value)
 * @method static Builder|Template whereContent($value)
 * @method static Builder|Template whereCreatedAt($value)
 * @method static Builder|Template whereDeputyIndustry($value)
 * @method static Builder|Template whereEnabled($value)
 * @method static Builder|Template whereExample($value)
 * @method static Builder|Template whereId($value)
 * @method static Builder|Template wherePrimaryIndustry($value)
 * @method static Builder|Template whereTemplateid($value)
 * @method static Builder|Template whereTitle($value)
 * @method static Builder|Template whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Template extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'app_id', 'title', 'templateid',
        'primary_industry', 'deputy_industry',
        'content', 'example', 'enabled',
    ];
    
    /**
     * 返回指定模板所属的应用对象
     *
     * @return BelongsTo
     */
    function app() { return $this->belongsTo('App\Models\App'); }
    
    /**
     * 返回模板列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Template.id', 'dt' => 0],
            ['db' => 'Template.title', 'dt' => 1],
            [
                'db'        => 'App.name', 'dt' => 2,
                'formatter' => function ($d) {
                    return sprintf(Snippet::ICON, 'fa-weixin text-green', '') .
                        '<span class="text-green">' . $d . '</span>';
                },
            ],
            ['db' => 'Template.primary_industry', 'dt' => 3],
            ['db' => 'Template.deputy_industry', 'dt' => 4],
            [
                'db'        => 'Template.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'apps',
                'alias'      => 'App',
                'type'       => 'INNER',
                'conditions' => [
                    'App.id = Template.app_id',
                ],
            ],
            [
                'table'      => 'corps',
                'alias'      => 'Corp',
                'type'       => 'INNER',
                'conditions' => [
                    'Corp.id = App.corp_id',
                ],
            ],
        ];
        $condition = 'App.corp_id = ' . (new Corp)->corpId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 设置所属行业
     *
     * @param TemplateRequest $request
     * @return bool
     * @throws Throwable
     */
    function config(TemplateRequest $request) {
        
        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();
                $app = App::find($data['app_id']);
                $token = Wechat::token('pub', $app->appid, $app->appsecret);
                $params = [$data['primary'], $data['secondary']];
                $result = Wechat::invoke(
                    'pub', 'template', 'api_set_industry' [$token],
                    array_combine(['industry_id1', 'industry_id2'], $params),
                );
                throw_if(
                    $errcode = $result['errcode'],
                    new Exception(Constant::WXERR[$errcode])
                );
                $app->update(
                    array_combine([
                        'properties->primary_industry',
                        'properties->second_industry',
                    ], $params)
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除模板
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge(['Template'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @return array
     */
    function compose() {
        
        switch (explode('/', Request::path())[1]) {
            case 'index':
                return [
                    'titles'  => [
                        '#', '标题', '公众号', '一级行业', '二级行业', '状态 . 操作',
                    ],
                    'buttons' => [
                        'config' => [
                            'id'    => 'create',
                            'label' => '设置所属行业',
                            'icon'  => 'fa fa-industry',
                        ],
                    ],
                    'batch'   => true,
                    'filter'  => true,
                ];
            default:
                return [
                    'apps'       => App::where('category', 2)
                        ->pluck('name', 'id')->toArray(),
                    'industries' => Constant::INDUSTRY,
                ];
        }
        
    }
    
}
