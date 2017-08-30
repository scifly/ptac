<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educatorAttendanceSetting['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $educatorAttendanceSetting['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '20',
                        'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
                @include('partials.single_select', [
                    'label' => '所属学校',
                    'id' => 'school_id',
                    'items' => $schools
                ])
                <div class="form-group">
                    {!! Form::label('start', '起始时间',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('start', null, ['class' => 'form-control start-date',]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('end', '起始时间',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('end', null, ['class' => 'form-control end-date',]) !!}
                    </div>
                </div>


                <div class="form-group">
                <label for="enabled" class="col-sm-4 control-label">
                    进或出
                </label>
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="inorout" type="checkbox" name="inorout" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($educatorAttendanceSetting['inorout'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
            {{--@include('partials.enabled', ['enabled' => isset( $educatorAttendanceSetting['enabled']) ? $educatorAttendanceSetting['enabled'] : ''])--}}
        </div>
    </div>
    @include('partials.form_buttons')
</div>
