<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
<<<<<<< HEAD
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
=======
            @if (!empty($procedure['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $procedure['id']]) }}
            @endif

            @include('partials.single_select', [
             'label' => '所属学校',
             'id' => 'school_id',
             'items' => $schools
            ])
             @include('partials.single_select', [
             'label' => '流程类型',
             'id' => 'procedure_type_id',
             'items' => $procedureTypes
>>>>>>> refs/remotes/origin/master
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
<<<<<<< HEAD
            @include('partials.enabled', ['enabled' => $procedure['enabled']])
=======
            {{--<div class="form-group">--}}
            {{--{!! Form::label('enabled', '是否启用', [--}}
            {{--'class' => 'col-sm-4 control-label'--}}
            {{--]) !!}--}}
            {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
            {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
            {{--data-theme="default" data-switchery="true"--}}
            {{--@if(!empty($procedure['enabled'])) checked @endif--}}
            {{--data-classname="switchery switchery-small"/>--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => isset($procedure['enabled']) ? $procedure['enabled'] : ''])
>>>>>>> refs/remotes/origin/master
        </div>
    </div>
    @include('partials.form_buttons')
</div>
