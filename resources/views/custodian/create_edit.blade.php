<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $custodian['id']]) }}
            @endif
            {{--<div class="form-group">--}}
            {{--{!! Form::label('user_id', '监护人姓名',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('user_id', $custodianName, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '监护人姓名',
                'id' => 'user_id',
                'items' => $custodianName
            ])
            <div class="form-group">
                {!! Form::label('expiry', '服务到期日期',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('relationship', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能少于2个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $custodian['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
