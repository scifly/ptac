<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($pqsc) && !empty($pqsc['id']))
                {{ Form::hidden('id', $pqsc['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '所属题目',
                'id' => 'pqs_id',
                'items' => $pqs
            ])
            <div class="form-group">
                {!! Form::label('choice', '选项内容', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('choice', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入选项内容',
                        'required' => 'true',
                        'data-parsley-length' => '[0, 40]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('seq_no', '排序编号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('seq_no', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入数字',
                        'required' => 'true',
                        'type' => "number",
                        'maxlength' => '3',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>