<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($department['id']))
                {{ Form::hidden('id', $department['id'], ['id' => 'id']) }}
            @endif
            {{ Form::hidden('parent_id', isset($parentId) ? $parentId : null, ['id' => 'parent_id']) }}
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa fa-sitemap'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入部门名称)',
                            'required' => 'true',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $department['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>

