<div class="box box-default box-solid main-form">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($tag))
                {!! Form::hidden('id', $tag['id'], ['id' => 'id']) !!}
            @endif
            <!-- 标签名称 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-tag'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过32个汉字)',
                            'required' => 'true',
                            'maxlength' => '32'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 部门/用户 -->
            <div class="form-group">
                @include('shared.label', ['field' => 'targets', 'label' => '部门/用户'])
                <div class="col-sm-6">
                    <div id="checked-nodes">{!! $targets ?? '' !!}</div>
                    {!! Form::hidden('selected-node-ids', $targetIds ?? null, [
                        'id' => 'selected-node-ids',
                    ]) !!}
                    {!! Form::button('<i class="fa fa-user-plus text-blue">&nbsp;选择</i>', [
                        'id' => 'choose',
                        'class' => 'btn btn-box-tool',
                        'style' => 'margin-top: 3px;'
                    ]) !!}
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $tag['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
