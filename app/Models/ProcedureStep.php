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

   public function approvers($approver_user_ids){

       $approvers_users_ids = explode('|',$approver_user_ids);

       $approvers = [];
       foreach ($approvers_users_ids as $auid) {
           $approver = Custodian::find($auid);
           $userId = $approver->user_id;
           $user = User::find($userId);
           $approvers[]['user'] = $user;
           $approvers[]['custodian'] = $approver;
       }

       return $approvers;

   }

    /**
     * @param $approver_user_ids
     * @return array
     */
    public function approvers_show($approver_user_ids){

        $approvers_users_ids = explode('|',$approver_user_ids);

        $approvers = [];
        foreach ($approvers_users_ids as $auid) {
            $approver = Custodian::find($auid);
            $userId = $approver->user_id;
            $user = User::find($userId);
            $approvers[] = $user;
        }

        return $approvers;
    }

    public function related_users($related_user_ids){

        $related_users_ids = explode('|',$related_user_ids);

        $related_users = [];
        foreach ($related_users_ids as $ruid) {
            $related_user = Custodian::find($ruid);
            $userId = $related_user->user_id;
            $user = User::find($userId);
            $related_users[]['user'] = $user;
            $related_users[]['custodian'] = $related_user;
        }

        return $related_users;

    }

    /**
     * @param $related_user_ids
     * @return array
     */
    public function related_users_show($related_user_ids){

        $related_users_ids = explode('|',$related_user_ids);

        $related_users = [];
        foreach ($related_users_ids as $ruid) {
            $related_user = Custodian::find($ruid);
            $userId = $related_user->user_id;
            $user = User::find($userId);
            $related_users[] = $user;
        }

        return $related_users;
    }


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
                   $users = $this->approvers($d);
                   $data = '';
                   foreach($users as $user) {

                       if(isset($user['user'])){
                           $arr = $user['user'];
                           $data .= $arr->realname . ', ';
                       }
                   }
                   return $data;
               }
           ],
           [
               'db' => 'ProcedureStep.related_user_ids', 'dt' => 3,
               'formatter' => function($d, $row) {
                   $ruids = $this->related_users($d);
                   $relateData = '';
                   foreach($ruids as $ruid) {

                       if(isset($ruid['user'])){
                           $arr = $ruid['user'];
                           $relateData .= $arr->realname . ', ';
                       }
                   }
                   return $relateData;
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
}
