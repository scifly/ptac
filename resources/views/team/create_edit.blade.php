<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'data-parsley-required' => 'true',
                        'max' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'max' => '255'
                    ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-3 control-label">--}}
                    {{--是否启用--}}
                {{--</label>--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($team['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $team['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
