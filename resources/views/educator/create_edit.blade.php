<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($educator) && !empty($educator['id']))
                {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            @endif

            @include('partials.single_select', [
                'label' => '教职员工',
                'id' => 'user_id',
                'items' => $users
            ])

            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
                @include('partials.multiple_select', [
                'label' => '所属班级',
                'id' => 'class_id',
                'items' => $squads
            ])
            <div class="form-group">
                {!! Form::label('sms_quote', '可用短信条数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('sms_quote', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($educator['enabled']) ? $educator['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
