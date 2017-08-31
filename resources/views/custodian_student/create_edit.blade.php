<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodianStudent['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $custodianStudent['id']]) }}
            @endif
            {{--<div class="form-group">--}}
            {{--{!! Form::label('custodian_id', '监护人姓名',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('custodian_id', $custodianName, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '监护人姓名',
                'id' => 'custodian_id',
                'items' => $custodianName
            ])
            {{--<div class="form-group">--}}
            {{--{!! Form::label('student_id', '学生姓名',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('student_id', $studentName, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '学生姓名',
                'id' => 'student_id',
                'items' => $studentName
            ])
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
            @include('partials.enabled', ['enabled' => isset($custodianStudent['enabled']) ? $custodianStudent['enabled']:""])
        </div>
    </div>
    @include('partials.form_buttons')
</div>

