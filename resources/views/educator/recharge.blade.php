{!! Form::model($educator, [
    'method' => 'put',
    'id' => 'formEducator',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            <div class="form-group">
                {!! Form::label('sms_quote', '余额', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <span id="quote" style="margin-top: 6px; display: block;">
                        {!! $educator['sms_quote'] !!} 条
                    </span>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('charge', '充值条数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('charge', null, [
                        'id' => 'charge',
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入充值条数)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
</div>
{!! Form::close() !!}
