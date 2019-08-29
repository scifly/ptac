<div class="form-group">
    {!! Form::label('mobile', '手机', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%;">
            <table id="mobiles" class="table-bordered table-responsive"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    @foreach (['号码', '默认', '启用', '+/-'] as $title)
                        <td class="text-center">{!! $title !!}</td>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @if(!empty($mobiles))
                        @foreach($mobiles as $key => $mobile)
                            <tr>
                                <td class="text-center">
                                    <div class="input-group">
                                        @include('shared.icon_addon', ['class' => 'fa-mobile'])
                                        {!! Form::text("mobile{$key}[mobile]", $mobile->$mobile, [
                                            'class' => 'form-control',
                                            'placeholder' => '(请输入手机号码)',
                                            'required',
                                            'pattern' => '/^1[0-9]{10}$/',
                                            'style' => 'width: 100%;'
                                        ]) !!}
                                        {!! Form::hidden("mobile[{$key}][id]", $mobile->id, [
                                            'class' => 'form-control'
                                        ]) !!}
                                    </div>
                                </td>
                                <td class="text-center">
                                    {!! Form::radio('mobile[isdefault]', $key, $mobile->isdefault, [
                                        'id' => "mobile[isdefault]{$key}",
                                        'title' => '默认手机号码',
                                        'class' => 'minimal',
                                        'required'
                                    ]) !!}
                                </td>
                                <td class="text-center">
                                    {!! Form::label("mobile[{$key}][enabled]") !!}
                                    {!! Form::checkbox("mobile[{$key}][enabled]", $mobile->enabled, $mobile->enabled, [
                                        'id' => "mobile[{$key}][enabled]", 'class' => 'minimal'
                                    ]) !!}
                                </td>
                                <td class="text-center">
                                    @if ($key == sizeof($mobiles) - 1)
                                        <span class="input-group-btn">
                                            {!! Form::button(
                                                Html::tag('i', '', ['title' => '新增', 'class' => 'fa fa-plus text-blue']),
                                                ['class' => 'btn btn-box-tool btn-add btn-mobile-add']
                                            ) !!}
                                        </span>
                                    @else
                                        <span class="input-group-btn">
                                            {!! Form::button(
                                                Html::tag('i', '', ['title' => '删除', 'class' => 'fa fa-minus text-blue']),
                                                ['class' => 'btn btn-box-tool btn-remove btn-mobile-remove']
                                            ) !!}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <!-- 手机号码数量 -->
                        {!! Form::hidden('count', sizeof($mobiles), ['id' => 'count', 'class' => 'form-contol']) !!}
                    @else
                        <tr>
                            <td class="text-center">
                                <div class="input-group">
                                    @include('shared.icon_addon', ['class' => 'fa-mobile'])
                                    {!! Form::text('mobile[0][mobile]', '', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入手机号码)',
                                        'required',
                                        'pattern' => '/^1[0-9]{10}$/',
                                        'style' => 'width: 100%;'
                                    ]) !!}
                                </div>
                            </td>
                            <td class="text-center">
                                {!! Form::radio('mobile[isdefault]', 0, true, [
                                    'id' => 'mobile[isdefault]',
                                    'title' => '默认手机号码',
                                    'class' => 'minimal'
                                ]) !!}
                            </td>
                            <td class="text-center">
                                {!! Form::label('mobile[0][enabled]') !!}
                                {!! Form::checkbox('mobile[0][enabled]', null, true, [
                                    'id' => 'mobile[0][enabled]',
                                    'class' => 'minimal'
                                ]) !!}
                            </td>
                            <td class="text-center">
                                <span class="input-group-btn">
                                    {!! Form::button(
                                        Html::tag('i', '', ['title' => '新增', 'class' => 'fa fa-plus text-blue']),
                                        ['class' => 'btn btn-box-tool btn-add btn-mobile-add']
                                    ) !!}
                                </span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>