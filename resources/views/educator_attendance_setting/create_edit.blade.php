<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educatorAttendanceSetting['id']))
                {{ Form::hidden('id', $educatorAttendanceSetting['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            <div class="form-group">
                {!! Form::label('start', '起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('start', null, [
                        'class' => 'form-control start-date',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('end', null, [
                        'class' => 'form-control end-date',
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '进或出',
                'id' => 'inorout',
                'value' => isset($educatorAttendanceSetting['inorout']) ? $educatorAttendanceSetting['inorout'] : NULL
            ])

        </div>
    </div>
    @include('partials.form_buttons')
</div>
