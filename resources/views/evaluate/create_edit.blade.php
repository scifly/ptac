<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($evaluate))
                {!! Form::hidden('id', $evaluate['id']) !!}
            @endif
            <!-- 考核对象 -->
            @include('shared.single_select', [
                'id' => 'student_id',
                'label' => '学生',
                'items' => $students,
                'icon' => 'fa fa-child'
            ])
            <!-- 考核项 -->
            @include('shared.single_select', [
                'id' => 'indicator_id',
                'label' => '考核项',
                'items' => $indicators,
                'icon' => 'fa fa-list'
            ])
            <!-- 学期 -->
            @include('shared.single_select', [
                'id' => 'semester_id',
                'label' => '学期',
                'items' => $semesters
            ])
            <!-- 分数 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'amount', 'label' => '分数'])
                <div class="col-sm-6">
                    {!! Form::number('amount', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                    ]) !!}
                </div>
            </div>
            <!-- 说明 -->
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $evaluate['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>