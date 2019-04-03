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
                <div>
                    @include('shared.single_select', [
                        'label' => '部门',
                        'id' => 'section_id',
                        'items' => $sections,
                        'icon' => 'fa fa-sitemap'
                    ])
                    <div class="form-group">
                        {!! Form::label('user_ids', '一卡通列表', [
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
                                    <td colspan="3" class="text-center">
                                        - 请选择一个部门进行一卡通批量授权 -
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <table class="table">
                    <tbody>
                    <tr>
                        @foreach (['#', '门禁', 'No.1', 'No.2', 'No.3', 'No.4'] as $title)
                            <th class="text-center" style="vertical-align: middle">
                                {!! $title !!}
                            </th>
                        @endforeach
                    </tr>
                    {!! $turnstiles !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('shared.form_buttons', ['id' => 'permit'])
    {!! Form::close() !!}
</div>