<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educatorClass['id']))
                {{ Form::hidden('id', $educatorClass['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '教职工姓名',
                'id' => 'educator_id',
                'items' => $users
            ])
            @include('partials.single_select', [
                'label' => '班级名称',
                'id' => 'class_id',
                'items' => $squad
            ])
            @include('partials.single_select', [
                'label' => '科目名称',
                'id' => 'subject_id',
                'items' => $subject
            ])
            @include('partials.enabled', ['enabled' => $educatorClass['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
