<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($corp['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $corp['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过60个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '3',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('company_id', '所属运营者',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('company_id', $companies, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('corpid', '企业号ID',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('corpid', null, [
                        'class' => 'form-control',
                        'placeholder' => '(36个小写字母与阿拉伯数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'alphanum'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-4 control-label">
                    是否启用
                </label>
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($corp['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
