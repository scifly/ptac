<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodianStudent['id']))
                {{ Form::hidden('id', $custodianStudent['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '监护人姓名',
                'id' => 'custodian_id',
                'items' => $custodianName
            ])
            @include('partials.single_select', [
                'label' => '学生姓名',
                'id' => 'student_id',
                'items' => $studentName
            ])
            <div class="form-group">
                {!! Form::label('relationship', '关系', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('relationship', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得少于2个汉字)',
                        'required' => 'true',
                        'minlength' => '2',
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($custodianStudent['enabled']) ? $custodianStudent['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>

