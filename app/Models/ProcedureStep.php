<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\ProcedureStep
 *
 * @property int $id
 * @property int $procedure_id 流程ID
 * @property string $name 流程步骤名称
 * @property string $approver_user_ids 审批人用户IDs
 * @property string $related_user_ids 相关人用户IDs
 * @property string $remark 流程步骤备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ProcedureStep whereApproverUserIds($value)
 * @method static Builder|ProcedureStep whereCreatedAt($value)
 * @method static Builder|ProcedureStep whereEnabled($value)
 * @method static Builder|ProcedureStep whereId($value)
 * @method static Builder|ProcedureStep whereName($value)
 * @method static Builder|ProcedureStep whereProcedureId($value)
 * @method static Builder|ProcedureStep whereRelatedUserIds($value)
 * @method static Builder|ProcedureStep whereRemark($value)
 * @method static Builder|ProcedureStep whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Procedure $procedure
 */
class ProcedureStep extends Model {
    //
   protected $table = 'procedure_steps';

   protected $fillable = [
       'procedure_id',
       'name',
       'approver_user_ids',
       'related_user_ids',
       'remark',
       'created_at',
       'updated_at',
       'enabled'
   ];

    public function procedure(){

        return $this->belongsTo('App\Models\Procedure');

    }

   public function datatable(){

       $columns = [
           ['db' => 'ProcedureStep.id', 'dt' => 0],
           ['db' => 'Procedures.name as procedurename', 'dt' => 1],
           [
               'db' => 'ProcedureStep.approver_user_ids', 'dt' => 2,
               'formatter' => function($d, $row) {
                   $users = $this->operate_ids($d);
                   $data = '';
                   foreach(array_keys($users) as $uid) {
                       $data .= $users[$uid]. ', ';
                   }
                   return $data;
               }
           ],
           [
               'db' => 'ProcedureStep.related_user_ids', 'dt' => 3,
               'formatter' => function($d, $row) {
                   $users = $this->operate_ids($d);
                   $data = '';
                   foreach(array_keys($users) as $uid) {
                       $data .= $users[$uid]. ', ';
                   }
                   return $data;
               }
           ],
           ['db' => 'ProcedureStep.name', 'dt' => 4],
           ['db' => 'ProcedureStep.remark', 'dt' => 5],
           ['db' => 'ProcedureStep.created_at', 'dt' => 6],
           ['db' => 'ProcedureStep.updated_at', 'dt' => 7],
           [
               'db' => 'ProcedureStep.enabled', 'dt' => 8,
               'formatter' => function ($d, $row) {
                   return Datatable::dtOps($this, $d, $row);
               }
           ],
       ];

       $joins = [
           [
               'table' => 'procedures',
               'alias' => 'Procedures',
               'type' => 'INNER',
               'conditions' => [
                   'Procedures.id = ProcedureStep.procedure_id'
               ]
           ]
       ];

       return Datatable::simple($this, $columns, $joins);
   }

    /**
     * 拆分appover_user_ids、related_user_ids,
     * @param $user_ids '|'符号拼接的教职工id字符串
     * @return array 处理后字典 key=>user.id,value => user.realname
     */
    public function operate_ids($user_ids){

        $user_ids = explode(',',$user_ids);

        $educators = array();
        foreach ($user_ids as $auid) {
//            $educator = Educator::find($auid);
//            $userId = $educator->user_id;
            $user = User::find($auid);
            $educators[$auid] = $user->username;
        }

        return $educators;
    }

    /**
     * 使用'|', 拼接教职工id
     * @param $arry_id
     * @return string
     */
    public function join_ids($arry_id){
        return implode(',',$arry_id);
    }
}
