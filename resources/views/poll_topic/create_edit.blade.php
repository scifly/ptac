<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($topic))
                {!! Form::hidden('id', $topic['id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'topic', 'label' => '题目名称'])
                <div class="col-sm-6">
                    {!! Form::text('topic', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('shared.single_select', [
                'id' => 'poll_id',
                'label' => '所属问卷',
                'items' => $polls,
            ])
            @include('shared.single_select', [
                'id' => 'category',
                'label' => '题目类型',
                'disabled' => isset($topic) ? true : null,
                'items' => $categories
            ])
            <div class="form-group" id="options" style="display: {!! isset($options) ? 'block' : 'none' !!};">
                @include('shared.label', ['field' => 'option', 'label' => '题目选项'])
                <div class="col-sm-6">
                    <table class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th class="text-center" style="vertical-align: middle;">选项</th>
                            <th class="text-center">
                                {!! Form::button(
                                    Html::tag('i', '', ['class' => 'fa fa-plus text-blue']),
                                    ['class' => 'btn btn-box-tool add-option', 'title' => '新增']
                                ) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>{!! $options !!}</tbody>
                    </table>
                </div>
            </div>
            @include('shared.switch', [

                'value' => $topic['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>