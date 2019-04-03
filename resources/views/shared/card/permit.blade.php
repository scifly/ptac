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
                    {!! Form::select('section_id', $sections, null, [
                        'class' => 'form-control select2',
                        'style' => 'width: 100%;',
                        'disabled' => sizeof($sections) <= 1
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('user_ids', '一卡通列表') !!}
                    <div>
                        <table style="width: 100%"
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
            <div class="col-md-9">
                <div class="form-group">
                    {!! Form::label('user_ids', '门禁列表') !!}
                    <div>
                        <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                            <thead>
                            <tr>
                                @foreach (['#', '门禁', 'No.1', 'No.2', 'No.3', 'No.4'] as $title)
                                    <th class="text-center" style="vertical-align: middle">
                                        {!! $title !!}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>{!! $turnstiles !!}</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons', ['id' => 'permit'])
    {!! Form::close() !!}
</div>