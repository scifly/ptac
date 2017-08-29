<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($class['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $class['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '4',
                        'data-parsley-maxlength' => '40'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades
            ])
            @include('partials.multiple_select', [
                'label' => '班级主任',
                'for' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => isset($selectedEducators) ? $selectedEducators : NULL
            ])
            @include('partials.enabled', ['enabled' => $class['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>