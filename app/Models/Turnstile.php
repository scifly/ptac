<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\BelongsToMany};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Turnstile 门禁设备
 *
 * @property int $id
 * @property string $sn 门禁设备名称
 * @property string $doors 门数
 * @property string $ip ip地址
 * @property string $port 端口号
 * @property string $location 门禁设备安装地点
 * @property int $school_id 所属学校ID
 * @property string $deviceid 门禁设备id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 门禁状态
 * @property-read School $school
 * @property-read Collection|PassageRule[] $passageRules
 * @method static Builder|Turnstile whereCreatedAt($value)
 * @method static Builder|Turnstile whereEnabled($value)
 * @method static Builder|Turnstile whereId($value)
 * @method static Builder|Turnstile whereIp($value)
 * @method static Builder|Turnstile wherePort($value)
 * @method static Builder|Turnstile whereLocation($value)
 * @method static Builder|Turnstile whereDeviceid($value)
 * @method static Builder|Turnstile whereSn($value)
 * @method static Builder|Turnstile whereSchoolId($value)
 * @method static Builder|Turnstile whereUpdatedAt($value)
 * @method static Builder|Turnstile newModelQuery()
 * @method static Builder|Turnstile newQuery()
 * @method static Builder|Turnstile query()
 * @method static Builder|Turnstile whereDoors($value)
 * @mixin Eloquent
 */
class Turnstile extends Model {
    
    use ModelTrait;
    
    const USER = 'test1@qq.com';
    const PWD = '12345678';
    const URL = 'http://api.ddd/api/';
    
    protected $table = 'turnstiles';
    
    protected $fillable = [
        'sn', 'doors', 'ip', 'port',
        'location', 'school_id',
        'deviceid', 'enabled',
    ];
    
    /**
     * 返回门禁设备所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定门禁包含的所有通行规则对象
     *
     * @return BelongsToMany
     */
    function passageRules() {
        
        return $this->belongsToMany(
            'App\Models\PassageRule',
            'rules_turnstiles',
            'turnstile_id',
            'passage_rule_id'
        );
        
    }
    
    /**
     * 门禁设备列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Turnstile.id', 'dt' => 0],
            ['db' => 'Turnstile.sn', 'dt' => 1],
            ['db' => 'Turnstile.location', 'dt' => 2],
            ['db' => 'Turnstile.doors', 'dt' => 3],
            ['db' => 'Turnstile.deviceid', 'dt' => 4],
            ['db' => 'Turnstile.created_at', 'dt' => 5],
            ['db' => 'Turnstile.updated_at', 'dt' => 6],
            [
                'db'        => 'Turnstile.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, true, false);
                },
            ],
        ];
        $condition = 'Turnstile.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 更新门禁设备列表
     *
     * @return bool
     * @throws Throwable
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $devices = $this->invoke('devlist');
                foreach ($devices as $device) {
                    $data = array_combine($this->fillable, [
                            $device['sn'], $device['doors'], $device['ip'],
                            $device['port'], $device['location'], $this->schoolId(),
                            $device['deviceid'], $device['enabled'],
                        ]
                    );
                    !($turnstile = $this->whereDeviceid($device['deviceid'])->first())
                        ? $this->create($data)
                        : $turnstile->update($data);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 返回门列表
     *
     * @return array
     */
    function doors() {
        
        $i = 0;
        foreach ($this->whereSchoolId($this->schoolId())->get() as $t) {
            $door = $t->sn . '.%s.' . $t->location;
            for ($j = 1; $j <= $t->doors; $j++) {
                $doors[$i++] = sprintf($door, $j);
            }
        }
        
        return $doors ?? [];
        
    }
    
    /**
     * 返回门禁设备ID
     *
     * @param $doorIds
     * @return array
     */
    function deviceids(array $doorIds) {
        
        $doors = $this->doors();
        foreach ($doorIds as $doorId) {
            $paths = explode('.', $doors[$doorId]);
            $deviceIds[] = $this->whereSn($paths[0])->first()->deviceid;
        }
        
        return array_unique($deviceIds ?? []);
        
    }
    
    /**
     * 调用接口
     *
     * @param string $api - 接口名称
     * @param array $params - 调用参数
     * @return mixed
     * @throws Throwable
     */
    function invoke($api, array $params = []) {
        
        try {
            $client = new Client;
            $token = session('token');
            if (!$token) {
                $response = $client->post(
                    self::URL . 'login', [
                        'form_params' => [
                            'email'    => self::USER,
                            'password' => self::PWD,
                        ],
                    ]
                )->getBody()->getContents();
                $token = json_decode($response, true)['token'];
                session(['token' => $token]);
            }
            $response = $client->post(
                self::URL . $api, [
                    'headers'     => ['Authorization' => 'Bearer ' . $token],
                    'form-params' => $params,
                ]
            );
            $body = json_decode($response->getBody(), true);
            $status = $response->getHeader('status');
            throw_if(
                $status == HttpStatusCode::INTERNAL_SERVER_ERROR,
                new Exception($body['msg'])
            );
            
            return $body['data'];
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}