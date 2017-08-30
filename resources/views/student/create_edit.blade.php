<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($student['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $student['id']]) }}
            @endif
            {{--<div class="form-group">--}}
            {{--{!! Form::label('user_id', '学生姓名',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('user_id', $user, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '学生姓名',
                'id' => 'user_id',
                'items' => $user
            ])
            {{--<div class="form-group">--}}
            {{--{!! Form::label('class_id', '班级名称',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('class_id', $class, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '班级名称',
                'id' => 'class_id',
                'items' => $class
            ])
            <div class="form-group">
                {!! Form::label('student_number', '学号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('student_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '小写字母与阿拉伯数字',
                     'data-parsley-type' => 'alphanum',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('card_number', '卡号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('card_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '小写字母与阿拉伯数字',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'alphanum',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('birthday', '生日',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('birthday', null, [
                    'class' => 'form-control',
                    'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '备注',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $student['oncampus'], 'label' => '是否住校'])
            @include('partials.enabled', ['enabled' => $student['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
