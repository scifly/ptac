<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{--<div class="form-group">--}}
            {{--{!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            {{--<div class="form-group">--}}
            {{--{!! Form::label('procedure_type_id', '流程类型',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('procedure_type_id', $procedureTypes, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '流程类型',
                'id' => 'procedure_type_id',
                'items' => $procedureTypes
            ])
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
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
                {!! Form::label('remark', '备注',[
                    'class' => 'col-sm-4 control-label',
                ]) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                         'placeholder' => '(不得超过80个汉字)',
                         'data-parsley-required' => 'true',
                         'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $procedure['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
