<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($messageType) && !empty($messageType['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $messageType['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '4',
                        'data-parsley-maxlength' => '40'
                    ]) !!}
                </div>
                {{--<div class="col-sm-5">--}}
                    {{--<p class="form-control-static text-danger">{{ $errors->first('name') }}</p>--}}
                {{--</div>--}}
            </div>

            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',
                    'data-parsley-maxlength' => '20'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
            'label' => '是否启用',
            'for' => 'enabled',
            'value' => isset($messageType['enabled']) ? $messageType['enabled'] : ''])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
