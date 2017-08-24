<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('procedure_id', '流程',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('procedure_id', $procedures, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('name', '步骤',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('approver_user_ids', '审批用户',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <select multiple="multiple" name="approver_user_ids[]" id="approver_user_ids" class="form-control">
                        <input type="hidden" id="approver_select_ids" value="{{ $procedureStep->approver_user_ids or '' }}">
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('related_user_ids', '相关人员',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <select multiple="multiple" name="related_user_ids[]" id="related_user_ids" class="form-control">
                        <input type="hidden" id="related_select_ids" value="{{$procedureStep->related_user_ids or '' }}">
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('enabled', '是否启用', [
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($procedureStep['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>

        </div>
    </div>
    @include('partials.form_buttons')
</div>
