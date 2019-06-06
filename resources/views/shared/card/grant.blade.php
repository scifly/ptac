@php
$styles = 'display nowrap table table-striped table-bordered table-hover table-condensed';
@endphp
<div class="box box-default box-solid">
    {!! Form::open([
        'method' => 'post',
        'id' => $formId,
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('section_id', '部门') !!}
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-sitemap'
                        ])
                        {!! Form::select('section_id', $sections, null, [
                            'class' => 'form-control select2',
                            'style' => 'width: 100%;',
                            'disabled' => sizeof($sections) <= 1
                        ]) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('user_ids', '一卡通列表') !!}
                    <div>
                        <table style="width: 100%" class="{!! $styles !!}">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle;" class="text-center">
                                    {!! Form::checkbox('contacts', 1, null, [
                                        'class' => 'minimal contacts',
                                    ]) !!}
                                </th>
                                @foreach (['姓名', '卡号'] as $title)
                                    <th style="vertical-align: middle;" class="text-center">
                                        {!! $title !!}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody id="section">
                            <tr>
                                <td colspan="3" class="text-center">
                                    - 请勾选需要授权的卡号 -
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-group bg">
                    {!! Form::label('daterange', '起止日期') !!}
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-calendar'
                        ])
                        {!! Form::text('daterange', null, [
                            'class' => 'form-control text-blue drange',
                            'placeholder' => '(起始日期 - 结束日期, 可选)'
                        ]) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('user_ids', '门禁列表') !!}
                    <div>
                        <table class="{!! $styles !!}">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle;" class="text-center">
                                    {!! Form::checkbox('gates', 2, null, [
                                        'class' => 'minimal gates'
                                    ]) !!}
                                </th>
                                @foreach (['门禁', '1', '2', '3', '4'] as $title)
                                    <th class="text-center" style="vertical-align: middle">
                                        {!! $title == '门禁' ? $title : '<span class="label bg-gray">' . $title . '</span>'!!}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                                @if (!empty($turnstiles))
                                    {!! $turnstiles !!}
                                @else
                                    <tr>
                                        <td class="text-center" colspan="6">
                                            - 请选择适用的门禁及通道规则 -
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons', ['id' => 'grant'])
    {!! Form::close() !!}
</div>