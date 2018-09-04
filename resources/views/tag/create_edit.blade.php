<div class="box box-default box-solid main-form">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tag['id']))
                {{ Form::hidden('id', $tag['id'], ['id' => 'id']) }}t
            @endif
            <!-- 标签名称 -->
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-tag'])
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
                {!! Form::label('targets', '部门/用户', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div id="checked-nodes">{!! $targets ?? '' !!}</div>
                    {!! Form::hidden('selected-node-ids', null, [
                        'id' => 'selected-node-ids',
                        'value' => $targetIds ?? null
                    ]) !!}
                    {!! Form::button('<i class="fa fa-user-plus text-blue">&nbsp;选择</i>', [
                        'id' => 'choose',
                        'class' => 'btn btn-box-tool',
                        'style' => 'margin-top: 3px;'
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $tag['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
