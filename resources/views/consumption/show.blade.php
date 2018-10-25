{!! Form::open([
    'method' => 'post',
    'id' => 'formConsumption',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @include('shared.single_select', [
                'label' => '统计范围',
                'id' => 'range_id',
                'items' => $ranges,
                'icon' => 'fa fa-bar-chart'
            ])
            @include('shared.single_select', [
                'label' => '学生',
                'id' => 'student_id',
                'divId' => 'students',
                'items' => $students,
                'icon' => 'fa fa-child'
            ])
            @include('shared.single_select', [
                'label' => '班级',
                'id' => 'class_id',
                'divId' => 'classes',
                'style' => 'display: none;',
                'items' => $classes,
                'icon' => 'fa fa-users'
            ])
            @include('shared.single_select', [
                'label' => '年级',
                'id' => 'grade_id',
                'divId' => 'grades',
                'style' => 'display: none;',
                'items' => $grades,
                'icon' => 'fa fa-object-group'
            ])
            <div class="form-group">
                {!! Form::label('name', '日期范围', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-default pull-right" id="daterange">
                            <span id="range">
                                <i class="fa fa-calendar"></i>&nbsp; 点击选择日期范围
                            </span>&nbsp;
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('result', '统计结果', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr class="text-bold">
                                <td>统计项</td>
                                <td class="text-right">金额</td>
                                <td class="text-right">明细</td>
                            </tr>
                            <tr>
                                <td class="text-red">总消费</td>
                                <td class="text-right text-red text-bold" id="a_consume"> - </td>
                                <td class="text-right detail">
                                    <button id="consume" title="点击查看详情" class="btn btn-box-tool" style="display: none;">
                                        <i class="fa fa-bars text-blue"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-green">总充值</td>
                                <td class="text-right text-green text-bold" id="a_charge"> - </td>
                                <td class="text-right detail">
                                    <button id="charge" title="点击查看详情" class="btn btn-box-tool" style="display: none;">
                                        <i class="fa fa-bars text-blue"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons', [
        'id' => 'stat',
        'label' => '开始统计'
    ])
</div>
{!! Form::close() !!}
<!-- 消费/充值明细 -->
<div class="modal fade" id="detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="detail_id" />
                <table class="table table-striped table-bordered" id="detail">
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a id="export" href="#" class="btn btn-sm btn-success" data-dismiss="modal">导出</a>
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
            </div>
        </div>
    </div>
</div>