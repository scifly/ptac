<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($school['id']))
                {{ Form::hidden('id', $school['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'data-parsley-length' => '[6, 255]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('address', '地址', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('address', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'data-parsley-length' => '[6, 255]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '学校类型',
                'id' => 'school_type_id',
                'items' => $schoolTypes
            ])
            @include('partials.single_select', [
                'label' => '所属企业',
                'id' => 'corp_id',
                'items' => $corps
            ])
            @if (isset($school['department_id']))
                {!! Form::hidden('department_id', $school['department_id']) !!}
            @endif
            @if (isset($school['menu_id']))
                {!! Form::hidden('menu_id', $school['menu_id']) !!}
            @endif
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($school['enabled']) ? $school['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
