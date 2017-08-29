<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($company['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $company['id']]) }}
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
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'data-parsley-required' => 'true'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('corpid', '企业号ID',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('corpid', null, [
                         'class' => 'form-control',
                         'placeholder' => '(36个小写字母与阿拉伯数字)',
                         'data-parsley-required' => 'true',
                         'data-parsley-type' => 'alphanum'
                     ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-4 control-label">--}}
                    {{--是否启用--}}
                {{--</label>--}}
                {{--<div class="col-sm-3" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($company['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => isset($company['enabled']) ? $company['enabled'] : ''])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
