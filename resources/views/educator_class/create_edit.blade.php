<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educatorClass['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $educatorClass['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('educator_id', '教职工姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('educator_id', $users, null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('class_id', '班级名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('class_id', $squad, null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('subject_id', '科目名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('subject_id', $subject, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $educatorClass['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
