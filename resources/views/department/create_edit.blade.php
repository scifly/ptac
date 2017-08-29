<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($department['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $department['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('custodian_id', '监护人姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('custodian_id', $custodianName, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student_id', '学生姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('student_id', $studentName, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('relationship', '关系',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('relationship', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能少于2个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-3 control-label">
                    是否启用
                </label>
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($custodianStudent['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
