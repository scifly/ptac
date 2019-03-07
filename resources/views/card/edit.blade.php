<div class="box box-default box-solid">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formCard',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '用户列表', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table id="simple-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-center">姓名</th>
                            <th class="text-center">角色</th>
                            <th>卡号</th>
                        </tr>
                        </thead>
                        <tbody>{!! $list !!}</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
    {!! Form::close() !!}
</div>