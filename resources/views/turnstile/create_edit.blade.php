<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($turnstile['id']))
                {{ Form::hidden('id', $turnstile['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('location', '安装地点', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! $turnstile['sn'] !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('location', '安装地点', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-location-arrow'])
                        {!! Form::text('location', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过80个汉字)',
                            'required' => 'true',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
</div>