<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        @include('shared.tree', [
            'title' => '规则作用范围',
            'selectedTitle' => '已选择的对象'
        ])
        <div class="form-horizontal main-form">
            @if (isset($pr))
                {!! Form::hidden('id', $pr['id']) !!}
            @endif
            <!-- 规则名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-reorder'])
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
                @include('shared.label', ['field' => 'related_ruleid', 'label' => '规则id'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::number('ruleid', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '请输入2-254范围内的整数',
                            'min' => 2,
                            'max' => 254
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 关联规则id -->
            @include('shared.single_select', [
                'label' => '关联规则',
                'id' => 'related_ruleid',
                'items' => $ruleids,
                'icon' => 'fa fa-reorder'
            ])
            <!-- 关联门禁 -->
            @include('shared.multiple_select', [
                'label' => '适用门禁通道',
                'id' => 'door_ids',
                'icon' => 'fa fa-minus-circle',
                'items' => $doors,
                'selectedItems' => $selectedDoors
            ])
            <!-- 起止日期 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'daterange', 'label' => '起止日期'])
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
            <!-- 通行日 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'weekdays[]', 'label' => '通行日'])
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead><tr>
                            @foreach (['一', '二', '三', '四', '五', '六', '日'] as $title)
                                <th class="text-center">{!! '周' . $title !!}</th>
                            @endforeach
                        </tr></thead>
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
            <!-- 通行时段 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'trs[][]', 'label' => '通行时段'])
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead><tr>
                            @foreach (['No.', '起', '止'] as $title)
                                <th class="text-center">{!! $title !!}</th>
                            @endforeach
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
