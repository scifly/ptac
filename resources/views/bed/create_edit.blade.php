<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($bed))
                {!! Form::hidden('id', $bed['id'], ['id' => 'id']) !!}
            @endif
            <!-- 名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-bed'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 所属寝室 -->
            @include('shared.single_select', [
                'id' => 'room_id',
                'label' => '所属寝室',
                'items' => $rooms,
                'icon' => 'fa fa-home'
            ])
            @include('shared.single_select', [
                'id' => 'student_id',
                'label' => '学生',
                'items' => $students,
                'icon' => 'fa fa-child'
            ])
            @include('shared.single_select', [
                'id' => 'position',
                'label' => '位置',
                'items' => ['-', '上铺', '下铺'],
                'icon' => 'fa fa-map-marker'
            ])
            <!-- 备注 -->
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $bed['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>