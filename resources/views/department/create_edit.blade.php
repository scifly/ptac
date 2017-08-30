<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($department['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $department['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '20',
                        'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能少于2个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('order', '次序值',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('order', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能少于2个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
                @include('partials.single_select', [
                      'label' => '所属父类',
                      'id' => 'parent_id',
                      'items' => $parents
                  ])
                @include('partials.single_select', [
                       'label' => '所属企业',
                       'id' => 'corp_id',
                       'items' =>$corps
                   ])
                @include('partials.single_select', [
                      'label' => '所属学校',
                      'id' => 'school_id',
                      'items' =>$schools
                  ])
                @include('partials.enabled', [
                'label' => '是否启用',
                'for' => 'enabled',
                'value' => isset($department['enabled'])?$department['enabled']:''])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
