<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($room))
                {!! Form::hidden('id', $room['id']) !!}
            @endif
            <!-- 名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-home'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 所属楼舍 -->
            @include('shared.single_select', [
                'id' => 'building_id',
                'label' => '所属楼舍',
                'items' => $buildings,
                'icon' => 'fa fa-building-o'
            ])
            <!-- 房间类型 -->
            @include('shared.single_select', [
                'id' => 'room_type_id',
                'label' => '房间类型',
                'items' => $rts,
            ])
            <!-- 所处楼层 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'floor', 'label' => '所处楼层'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-map-pin'])
                        {!! Form::number('floor', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 容量 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'volume', 'label' => '容量'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-users'])
                        {!! Form::number('volume', null, ['class' => 'form-control text-blue']) !!}
                    </div>
                </div>
            </div>
            <!-- 备注 -->
            @include('shared.remark')
            @include('shared.switch', [

                'value' => $room['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>