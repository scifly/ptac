<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($procedureStep['id']))
                {{ Form::hidden('id', $procedureStep['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '流程',
                'id' => 'procedure_id',
                'items' => $procedures
            ])
            <div class="form-group">
                {!! Form::label('name', '步骤', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('approver_user_ids', '审批用户', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <select multiple="multiple" name="approver_user_ids[]" id="approver_user_ids" class="form-control">
                        <input type="hidden" id="approver_select_ids"
                               value="{{ $procedureStep->approver_user_ids or '' }}">
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('related_user_ids', '相关人员', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <select multiple="multiple" name="related_user_ids[]" id="related_user_ids" class="form-control">
                        <input type="hidden" id="related_select_ids"
                               value="{{$procedureStep->related_user_ids or '' }}">
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($procedureStep['enabled']) ? $procedureStep['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
