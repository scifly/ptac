<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($pqSubject) && !empty($pqSubject['id']))
                {{ Form::hidden('id', $pqSubject['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('subject', '题目名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('subject', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属问卷',
                'id' => 'pq_id',
                'items' => $pq
            ])
            @include('partials.single_select', [
                'label' => '题目类型',
                'id' => 'subject_type',
                'items' => $subject_type
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>