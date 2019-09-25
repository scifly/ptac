<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
        @if (isset($building))
            {!! Form::hidden('id', $building['id']) !!}
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
                    ]) !!}
                </div>
            </div>
        </div>
        <!-- 楼层数 -->
        <div class="form-group">
            @include('shared.label', ['field' => 'floors', 'label' => '楼层数'])
            <div class="col-sm-6">
                <div class="input-group">
                    @include('shared.icon_addon', ['class' => 'fa-map-marker'])
                    {!! Form::number('floors', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                    ]) !!}
                </div>
            </div>
        </div>
        <!-- 备注 -->
        @include('shared.remark')
        @include('shared.switch', [

            'value' => $building['enabled'] ?? null
        ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>