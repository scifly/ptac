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
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('corp_id', '所属企业：', [
                    'class' => 'control-label',
                ]) !!}
                @if (Auth::user()->group->name == '运营')
                    {!! Form::select('corp_id', $corps, null, [
                        'class' => 'form-control input-sm'
                    ]) !!}
                @else

                    {!! Form::label('name', $corp->name, [
                        'class' => 'control-label',
                        'style' => 'font-weight: normal;'
                    ]) !!}
                @endif
            </div>
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('agentid', '企业应用id：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('agentid', null, [
                    'id' => 'agentid',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                ]) !!}
            </div>
            <div class="form-group" style="margin-right: 10px">
                {!! Form::label('secret', '应用Secret：', [
                    'class' => 'control-label'
                ]) !!}
                {!! Form::text('secret', null, [
                    'id' => 'secret',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                    'data-parsley-length' => '[44,44]'
                ]) !!}
            </div>
            {!! Form::submit('同步应用', [
                'id' => 'sync',
                'class' => 'btn btn-default'
            ]) !!}
        </div>
        {!! Form::close() !!}
        <div style="display: block; overflow-x: auto; clear: both; width: 100%;">
            <table class="table-striped table-bordered table-hover table-condensed"
               style="white-space: nowrap; width: 100%;">
            <thead>
            <tr>
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
                @foreach($apps as $app)
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
                                <span class="badge bg-green">已启用</span>
                            @else
                                <span class="badge bg-gray">未启用</span>
                            @endif
                            <a href="javascript:void(0)" class="btn btn-primary btn-xs">
                                修改
                            </a>
                            <a href="javascript:void(0)" class="btn bg-purple btn-xs">
                                同步菜单
                            </a>
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