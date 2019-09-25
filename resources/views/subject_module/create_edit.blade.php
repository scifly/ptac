<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($sm))
                {!! Form::hidden('id', $sm['id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过20个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 20]'
                    ]) !!}
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '所属科目',
                'id' => 'subject_id',
                'items' => $subjects
            ])
            <div class="form-group">
                {!! Form::label('weight', '次分类权重', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('weight', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '次分类权重是数字',
                        'required' => 'true',
                        'type' => 'range',
                        'data-parsley-range' => '[1, 100]'
                    ]) !!}
                </div>
            </div>
            @include('shared.switch', [

                'value' => $sm['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
