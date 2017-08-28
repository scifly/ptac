<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $custodian['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('user_id', '监护人姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('user_id', $user, null, ['class' => 'form-control']) !!}
                </div>
            </div>

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
            {{--<div class="form-group">--}}
                {{--<label for="enabled" class="col-sm-3 control-label">--}}
                    {{--是否启用--}}
                {{--</label>--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($custodian['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
    @include('partials.form_buttons')
</div>
