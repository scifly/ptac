<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($semester['id']))
                {!! Form::hidden('id', $semester['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('name', '起止日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        {!! Form::hidden('start_date', $semester['start_date'] ?? null, ['id' => 'start_date']) !!}
                        {!! Form::hidden('end_date', $semester['end_date'] ?? null, ['id' => 'end_date']) !!}
                        <button type="button" class="btn btn-default pull-right" id="daterange">
                            <span id="range">
                                @if (!isset($semester))
                                    <i class="fa fa-calendar"></i>&nbsp; 点击选择起止日期
                                @else
                                    {!! date('Y-m-d', strtotime($semester['start_date'])) !!} -
                                    {!! date('Y-m-d', strtotime($semester['end_date'])) !!}
                                @endif
                            </span>&nbsp;
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            {{--<div class="form-group">--}}
            {{--{!! Form::label('start_date', '起始日期', [--}}
            {{--'class' => 'col-sm-3 control-label'--}}
            {{--]) !!}--}}
            {{--<div class="col-sm-6">--}}
            {{--<div class="input-group date">--}}
            {{--@include('partials.icon_addon', ['class' => 'fa-calendar'])--}}
            {{--{!! Form::text('start_date', null, [--}}
            {{--'class' => 'form-control pull-right start_date',--}}
            {{--'placeholder' => '(请选择起始日期)',--}}
            {{--'required' => 'true',--}}
            {{--]) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
            {{--{!! Form::label('end_date', '结束日期', [--}}
            {{--'class' => 'col-sm-3 control-label'--}}
            {{--]) !!}--}}
            {{--<div class="col-sm-6">--}}
            {{--<div class="input-group date">--}}
            {{--@include('partials.icon_addon', ['class' => 'fa-calendar'])--}}
            {{--{!! Form::text('end_date', null, [--}}
            {{--'class' => 'form-control pull-right end_date',--}}
            {{--'placeholder' => '(请选择结束日期)',--}}
            {{--'required' => 'true',--}}
            {{--]) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $semester['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
