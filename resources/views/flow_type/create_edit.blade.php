<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($ft))
                {!! Form::hidden('id', $ft['id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'steps[]', 'label' => '审批步骤'])
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead><tr>
                            @foreach (['名称', '审批人'] as $title)
                                <th class="text-center" style="vertical-align: middle;">
                                    {!! $title !!}
                                </th>
                            @endforeach
                            <th class="text-center">
                                {!! Form::button(
                                    Html::tag('i', '', ['class' => 'fa fa-plus text-blue']),
                                    ['class' => 'btn btn-box-tool add-step', 'title' => '新增']
                                ) !!}
                            </th>
                        </tr></thead>
                        <tbody>{!! $steps !!}</tbody>
                    </table>
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', ['value' => $ft['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
