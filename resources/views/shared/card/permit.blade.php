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
                'label' => '部门',
                'id' => 'section_id',
                'items' => $sections,
                'icon' => 'fa fa-sitemap'
            ])
            @include('shared.multiple_select', [
                'label' => '通行门禁',
                'id' => 'turnstile_ids',
                'icon' => 'fa fa-minus-circle',
                'items' => $turnstiles
            ])
            <div class="form-group">
                {!! Form::label('name', '一卡通列表', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <table id="simple-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                            <tr>
                                @foreach (['姓名', '卡号', '授权'] as $title)
                                    <th style="vertical-align: middle;" class="text-center">
                                        {!! $title !!}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="{!! $columns !!}" class="text-center">
                                - 请选择一个部门进行批量一卡通授权 -
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons', ['id' => 'issue'])
    {!! Form::close() !!}
</div>