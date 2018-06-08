{!! Form::model($educator, [
    'method' => 'put',
    'id' => 'formsEducator',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            <div class="form-group">
                {!! Form::label('sms_quote', '余额', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! $educator['sms_quote'] !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('recharge', '充值条数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('recharge', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入充值条数)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}
