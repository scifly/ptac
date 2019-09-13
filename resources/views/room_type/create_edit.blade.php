<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
        @if (isset($rt))
            {!! Form::hidden('id', $rt['id'], ['id' => 'id']) !!}
        @endif
        <!-- 名称 -->
        <div class="form-group">
            @include('shared.label', ['field' => 'name', 'label' => '名称'])
            <div class="col-sm-6">
                <div class="input-group">
                    @include('shared.icon_addon', ['class' => 'fa-building-o'])
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
        </div>
        <!-- 功能类型 -->
        @include('shared.single_select', [
            'id' => 'room_function_id',
            'label' => '功能类型',
            'items' => $rfs,
            'icon' => 'fa fa-gear'
        ])
        <!-- 备注 -->
        @include('shared.remark')
        @include('shared.switch', [
            'id' => 'enabled',
            'value' => $rt['enabled'] ?? null
        ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>