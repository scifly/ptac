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
            {!! Form::hidden('id', null, ['id' => 'id']) !!}
            <!-- 所属企业 -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('corp_id', '所属企业：', [
                    'class' => 'control-label',
                ]) !!}
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-weixin text-green" style="width: 20px;"></i>
                    </div>
                    {!! Form::select('corp_id', $corps, null, [
                        'class' => 'form-control select2 input-sm',
                        'style' => 'width: 100%;',
                        'disabled' => sizeof($corps) <= 1
                    ]) !!}
                </div>
            </div>
            <!-- 企业应用名称 -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('name', '名称：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('name', null, [
                    'id' => 'name',
                    'class' => 'form-control input-sm text-blue',
                    'placeholder' => '(可选)'
                ]) !!}
            </div>
            <!-- 企业应用ID -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('agentid', 'agentid：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('agentid', null, [
                    'id' => 'agentid',
                    'class' => 'form-control input-sm text-blue',
                    'required' => 'true',
                ]) !!}
            </div>
            <!-- 应用Secret -->
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('secret', 'secret：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('secret', null, [
                    'id' => 'secret',
                    'class' => 'form-control input-sm text-blue',
                    'required' => 'true',
                    'data-parsley-length' => '[43,43]'
                ]) !!}
            </div>
            {!! Form::submit('同步/创建应用', [
                'id' => 'sync',
                'class' => 'btn btn-default btn-sm'
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
                <th class="text-center">agentid</th>
                <th class="text-center">名称</th>
                <th class="text-center">头像</th>
                <th class="text-center">secret</th>
                <th class="text-center">创建于</th>
                <th class="text-center">更新于</th>
                <th class="text-right">状态 . 操作</th>
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
                        <td class="text-center">
                            @if ($app['square_logo_url'] != '0')
                                <img class="img-circle" style="height: 16px;" src="{{ $app['square_logo_url'] }}" />
                            @endif
                        </td>
                        <td class="text-center">{{ $app['secret'] }}</td>
                        <td class="text-center">{{ $app['created_at'] }}</td>
                        <td class="text-center">{{ $app['updated_at'] }}</td>
                        <td class="text-right">
                            @if($app['enabled'])
                                <i class="fa fa-circle text-green" title="已启用"></i>
                            @else
                                <i class="fa fa-circle text-gray" title="未启用"></i>
                            @endif
                            &nbsp;&nbsp;&nbsp;
                            <a href="#"><i class="fa fa-pencil" title="编辑"></i></a>
                            &nbsp;&nbsp;
                            <a href="#"><i class="fa fa-remove text-red" title="删除"></i></a>
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