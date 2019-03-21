<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($pr['id']))
                {!! Form::hidden('id', $pr['id'], ['id' => 'id']) !!}
            @endif
            <!-- 规则名称 -->
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-credit-card'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '不能超过60个汉字',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 规则id -->
            <div class="form-group">
                {!! Form::label('related_ruleid', '规则id', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('ruleid', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 关联规则id -->
            <div class="form-group">
                {!! Form::label('name', '关联规则id', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('related_ruleid', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 起止日期 -->
            <div class="form-group">
                {!! Form::label('daterange', '起止日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-calendar'])
                        {!! Form::text('daterange', isset($pr) ? $pr->start_date . ' ~ ' . $pr->end_date : null, [
                            'class' => 'form-control text-blue drange',
                            'placeholder' => '(起始日期 - 结束日期)',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 生效日 -->
            <div class="form-group">
                {!! Form::label('weekdays[]', '生效日', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="text-center">周一</th>
                                <th class="text-center">周二</th>
                                <th class="text-center">周三</th>
                                <th class="text-center">周四</th>
                                <th class="text-center">周五</th>
                                <th class="text-center">周六</th>
                                <th class="text-center">周日</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($weekdays as $weekday => $enabled)
                                    <td class="text-center">
                                        {!! Form::checkbox(
                                            'weekdays[]', $weekday, $enabled,
                                            ['class' => 'minimal']
                                        ) !!}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- 时段 -->
            <div class="form-group">
                {!! Form::label('trs[][]', '生效时段', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead><tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">起</th>
                            <th class="text-center">止</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($trs as $key => $tr)
                            <tr>
                                <td style="vertical-align: middle" class="text-center">{!! $key + 1 !!}</td>
                                <td class="text-center">
                                    <div class="bootstrap-timepicker">
                                        <div class="input-group">
                                            {!! Form::text('trs[' . $key . '][]', $tr[0], [
                                                'class' => 'form-control start-time timepicker text-center',
                                                'required' => 'true',
                                                'data-parsley-start' => '.end-time'
                                            ]) !!}
                                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="bootstrap-timepicker">
                                        <div class="input-group">
                                            {!! Form::text('trs[' . $key . '][]', $tr[1], [
                                                'class' => 'form-control end-time timepicker text-center',
                                                'required' => 'true',
                                                'data-parsley-end' => '.start-time'
                                            ]) !!}
                                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $pr['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
