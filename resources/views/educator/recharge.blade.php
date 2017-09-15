{!! Form::model($educator, ['url' => '/educators/' . $educator->id, 'method' => 'put', 'id' => 'formEducator', 'data-parsley-validate' => 'true']) !!}
<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educator['id']))
                {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('user_id', '充值用户', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::select('user_id', $users, null, [
                        'class' => 'form-control',
                        'style' => 'width: 100%;',
                        'disabled' => 'disabled'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('sms_quote', '余额', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('sms_quote', null, [
                        'class' => 'form-control',
                        'disabled' => 'disabled'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('recharge', '充值条数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('recharge', null, [
                        'class' => 'form-control',
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
