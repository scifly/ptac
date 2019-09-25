<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($exam))
                {!! Form::hidden('id', $exam['id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>名</strong>
                        </div>
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '考试类型',
                'id' => 'exam_type_id',
                'items' => $examtypes
            ])
            @include('shared.multiple_select', [
                'label' => '参与班级',
                'id' => 'class_ids',
                'icon' => 'fa fa-users',
                'items' => $classes,
                'selectedItems' => $selectedClasses
            ])
            @include('shared.multiple_select', [
                'label' => '科目',
                'id' => 'subject_ids',
                'icon' => 'fa fa-book',
                'items' => $subjects,
                'selectedItems' => $selectedSubjects
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'daterange', 'label' => '起止日期'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-calendar'])
                        {!! Form::text('daterange', isset($exam) ? $exam->start_date . ' ~ ' . $exam->end_date : null, [
                            'class' => 'form-control text-blue drange',
                            'placeholder' => '(开始日期 - 结束日期)',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', ['value' => $exam['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
