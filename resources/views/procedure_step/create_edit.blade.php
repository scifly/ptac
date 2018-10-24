<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($ps['id']))
                {{ Form::hidden('id', $ps['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '流程',
                'id' => 'procedure_id',
                'items' => $procedures
            ])
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入步骤名称，不得超过20个汉字)',
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
                    <select multiple="multiple" name="approver_user_ids[]"
                            id="approver_user_ids" class="form-control" title="审批用户"
                    >
                        <input type="hidden" id="approver_select_ids" title="审批用户"
                               value="{{ $ps->approver_user_ids or '' }}">
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
                               value="{{$ps->related_user_ids or '' }}">
                    </select>
                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $ps['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
