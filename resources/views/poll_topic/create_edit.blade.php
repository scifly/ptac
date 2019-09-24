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
            <div class="form-group" id="options" style="display: {!! isset($content) ? 'block' : 'none' !!};">
                @include('shared.label', ['field' => 'option', 'label' => '选项'])
                <div class="col-sm-6">{!! $content !!}</div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $topic['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>