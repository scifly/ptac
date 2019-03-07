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
        <div class="form-horizontal">
            @include('shared.single_select', [
                'label' => '班级',
                'id' => 'class_id',
                'items' => $classes,
                'icon' => 'fa fa-users'
            ])
            <div class="form-group">
                {!! Form::label('name', $label, [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table id="simple-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            {!! $titles !!}
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="{!! $columns !!}" class="text-center">
                                - 请选择班级批量发卡 -
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
    {!! Form::close() !!}
</div>