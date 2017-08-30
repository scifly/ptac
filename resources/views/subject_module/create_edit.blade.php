<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '20',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
            {{--{!! Form::label('subject_id', '科目名称',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('subject_id', $subjects, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '科目名称',
                'id' => 'subject_id',
                'items' => $subjects
            ])
            <div class="form-group">
                {!! Form::label('weight', '次分类权重',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('weight', null, [
                    'class' => 'form-control',
                    'placeholder' => '次分类权重是数字',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'integer',

                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $subjectModules['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
