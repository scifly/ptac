<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($department))
                {!! Form::hidden('id', $department['id']) !!}
            @endif
            {!! Form::hidden('parent_id', $parentId ?? null) !!}
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
            <!-- 所属标签 -->
            @include('shared.tag.tags')
            @include('shared.remark')
            @include('shared.switch', [

                'value' => $department['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>

