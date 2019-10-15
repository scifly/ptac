<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Eloquent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsToMany};
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class Camera - 人脸识别设备
 *
 * @property int $id
 * @property int $cameraid
 * @property int $school_id
 * @property string $name
 * @property string $ip
 * @property string $mac
 * @property string|null $location
 * @property int|null $direction
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status
 * @property-read Collection|Face[] $faces
 * @property-read int|null $faces_count
 * @method static Builder|Camera newModelQuery()
 * @method static Builder|Camera newQuery()
 * @method static Builder|Camera query()
 * @method static Builder|Camera whereCameraid($value)
 * @method static Builder|Camera whereCreatedAt($value)
 * @method static Builder|Camera whereDirection($value)
 * @method static Builder|Camera whereId($value)
 * @method static Builder|Camera whereIp($value)
 * @method static Builder|Camera whereLocation($value)
 * @method static Builder|Camera whereMac($value)
 * @method static Builder|Camera whereName($value)
 * @method static Builder|Camera whereSchoolId($value)
 * @method static Builder|Camera whereStatus($value)
 * @method static Builder|Camera whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Camera extends Model {
    
    use ModelTrait;
    
    const USERNAME = 'test1@qq.com';
    const PASSWORD = '12345678';
    const BASE_URI = 'http://api.ddd/api/';
    
    protected $fillable = [
        'cameraid', 'school_id', 'name', 'ip',
        'mac', 'location', 'direction', 'status',
    ];
    
    /** @return BelongsToMany */
    function faces() { return $this->belongsToMany('App\Models\Face', 'camera_face'); }
    
    /**
     * 人脸识别设备列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Camera.id', 'dt' => 0],
            ['db' => 'Camera.name', 'dt' => 1],
            ['db' => 'Camera.ip', 'dt' => 2],
            ['db' => 'Camera.mac', 'dt' => 3],
            ['db' => 'Camera.location', 'dt' => 4],
            [
                'db'        => 'Camera.direction', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d ? '出' : '进';
                },
            ],
            ['db' => 'Camera.created_at', 'dt' => 6, 'dr' => true],
            ['db' => 'Camera.updated_at', 'dt' => 7, 'dr' => true],
            [
                'db'        => 'Camera.status', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, false, false);
                },
            ],
        ];
        $condition = 'Camera.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 更新人脸设备列表
     *
     * @return bool
     * @throws Throwable
     */
    function store() {
        
        try {
            DB::transaction(function () {
                $devices = $this->invoke('flist');
                $records = [];
                foreach ($devices as $device) {
                    $mac = $device['mac'];
                    $record = array_combine($this->fillable, [
                            $device['id'], $this->schoolId(), $device['name'],
                            $device['ip'], $mac, $device['location'],
                            $device['direction'], $device['status'],
                        ]
                    );
                    if ($camera = $this->whereMac($mac)->first()) {
                        $camera->update($record);
                    } else {
                        $records[] = $record;
                    }
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
    /**
     * 返回composer所需的view数据
     *
     * @return array
     */
    function compose() {
        
        return [
            'buttons' => [
                'store' => [
                    'id'    => 'store',
                    'label' => '刷新',
                    'icon'  => 'fa fa-refresh',
                ],
            ],
            'titles'  => [
                '#', '设备名称', 'ip', 'mac', '安装地点',
                '方向', '创建于', '更新于', '状态',
            ],
        ];
        
    }
    
    /**
     * 返回人脸是设备列表
     *
     * @return SCollection
     */
    function list() {
        
        return [0 => '【所有设备】'] + $this->all()->pluck('name', 'id');
        
    }
    
    /**
     * 调用接口
     *
     * @param string $uri - 接口名称
     * @param array $params - 调用参数
     * @return mixed
     * @throws Throwable
     */
    function invoke($uri, $params = null) {
        
        try {
            $client = new Client;
            if (!$token = session('token')) {
                $response = $client->post(
                    self::BASE_URI . 'login', [
                        'form_params' => [
                            'email'    => self::USERNAME,
                            'password' => self::PASSWORD,
                        ],
                    ]
                )->getBody()->getContents();
                $token = json_decode($response, true)['token'];
                session(['token' => $token]);
            }
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
            ];
            if ($params) {
                !is_string($params) ?: $params = ['raw' => $params];
                $options['form_params'] = $params;
            }
            $response = $client->post(self::BASE_URI . $uri, $options);
            $body = json_decode($response->getBody(), true);
            $status = $response->getHeader('status');
            throw_if(
                $status == Constant::INTERNAL_SERVER_ERROR,
                new Exception($body['msg'])
            );
        } catch (Exception $e) {
            throw $e;
        }
    
        return $body['data'];
        
    }
    
}
