<div class="box box-default box-solid">
    <div class="box-header with-border">
        <span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb ?? '' !!}</span>
        <div class="box-tools pull-right">
            <button id="all" type="button" class="btn btn-box-tool">
                <i class="fa fa-refresh text-blue"> 初始化所有参数</i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @include('shared.single_select', [
                'label' => '参数名称',
                'id' => 'param_id',
                'items' => $params,
                'icon' => 'fa fa-gear'
            ])
            <div class="form-group">
                {!! Form::label('name', '参数列表', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table id="simple-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">名称</th>
                            <th>备注</th>
                            <th>创建于</th>
                            <th class="text-right">状态</th>
                        </tr>
                        </thead>
                        <tbody>{!! $list !!}</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_overlay')
    <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <button id="init" class="btn btn-primary" type="submit">
                    <i class="fa fa-refresh"> 初始化当前参数</i>
                </button>
            </div>
        </div>
    </div>
</div>
