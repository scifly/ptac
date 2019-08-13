<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="action-type">
                    <a href="#tab02" data-toggle="tab">
                        <i class="fa fa-list"></i>&nbsp;发送记录
                    </a>
                </li>
                <li class="active action-type">
                    <a href="#tab01" data-toggle="tab">
                        <i class="fa fa-money"></i>&nbsp;短信充值
                    </a>
                </li>
                <li class="pull-left header">
                    <i class="fa fa-money"></i>短信充值 & 查询
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab01">
                    <div class="form-horizontal">
                        {!! Form::model($model, [
                            'method' => 'put',
                            'id' => $formId,
                            'data-parsley-validate' => 'true'
                        ]) !!}
                        {!! Form::hidden('id', $model['id'], ['id' => 'id']) !!}
                        <div class="form-group">
                            {!! Form::label('sms_balance', '余额', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <span id="quote" style="margin-top: 6px; display: block;">
                                    {!! $model['sms_balance'] ?? 0 !!} 条
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('charge', '条数', [
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
                        @include('shared.form_buttons')
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="tab-pane" id="tab02">
                    @include('message.list_sent')
                </div>
            </div>
        </div>
    </div>

</div>