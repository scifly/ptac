<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        {!! Form::open([
            'method' => 'post',
            'id' => 'formApp',
            'data-parsley-validate' => 'true'
        ]) !!}
        <div class="form-inline">
            <!-- 所属企业 -->
            @if (isset($corps))
                {{--@include('partials.single_select', [--}}
                    {{--'id' => 'corp_id',--}}
                    {{--'label' => '所属企业',--}}
                    {{--'icon' => 'fa fa-weixin',--}}
                    {{--'items' => $corps--}}
                {{--])--}}
                <div class="form-group" style="margin-right: 10px">
                    {!! Form::label('corp_id', '所属企业：', [
                        'class' => 'control-label',
                    ]) !!}
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-weixin" style="width: 20px;"></i>
                        </div>
                        {!! Form::select('corp_id', $corps, null, [
                            'class' => 'form-control select2 input-sm',
                            'style' => 'width: 100%;'
                        ]) !!}
                    </div>
                </div>
            @else
                <div class="form-group" style="margin-right: 10px">
                    {!! Form::label('corp_id', '所属企业：', [
                        'class' => 'control-label',
                    ]) !!}
                    {!! Form::label('name', $corp->name, [
                        'class' => 'control-label',
                        'style' => 'font-weight: normal;'
                    ]) !!}
                </div>
            @endif
            <!-- 企业应用ID -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('agentid', '应用AgentId：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('agentid', null, [
                    'id' => 'agentid',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                ]) !!}
            </div>
            <!-- 应用Secret -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('secret', '应用Secret：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('secret', null, [
                    'id' => 'secret',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                    'data-parsley-length' => '[43,43]'
                ]) !!}
            </div>
            {!! Form::submit('同步应用', [
                'id' => 'sync',
                'class' => 'btn btn-default'
            ]) !!}
        </div>
        {!! Form::close() !!}
        <!-- 企业应用列表 -->
        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
            <table class="table-striped table-bordered table-hover table-condensed"
               style="white-space: nowrap; width: 100%;">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th class="text-center">应用id</th>
                <th class="text-center">应用名称</th>
                <th class="text-center">应用头像</th>
                <th>应用详情</th>
                <th class="text-center">创建于</th>
                <th class="text-center">更新于</th>
                <th class="text-right">状态</th>
            </tr>
            </thead>
            <tbody>
            @if (sizeof($apps) == 0)
                <tr id="na">
                    <td colspan="8" style="text-align: center">( n/a )</td>
                </tr>
            @else
                @foreach ($apps as $app)
                    <tr id="app{{ $app['agentid'] }}">
                        <td>{{ $app['id'] }}</td>
                        <td class="text-center">{{ $app['agentid'] }}</td>
                        <td class="text-center">{{ $app['name'] }}</td>
                        <td class="text-center"><img style="width: 16px; height: 16px;" src="{{ $app['square_logo_url'] }}"/></td>
                        <td>{{ $app['description'] }}</td>
                        <td class="text-center">{{ $app['created_at'] }}</td>
                        <td class="text-center">{{ $app['updated_at'] }}</td>
                        <td class="text-right">
                            @if($app['enabled'])
                                <i class="fa fa-circle text-green" title="已启用"></i>
                            @else
                                <i class="fa fa-circle text-gray" title="未启用"></i>
                            @endif
                            &nbsp;&nbsp;
                            <a href="#"><i class="fa fa-pencil" title="修改"></i></a>
                            &nbsp;&nbsp;
                            <a href="#"><i class="fa fa-exchange" title="同步菜单"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        </div>
    </div>
    @include('partials.form_overlay')
</div>