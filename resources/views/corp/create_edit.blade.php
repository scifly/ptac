<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($corp['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $corp['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过60个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '3',
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属运营者',
                'id' => 'company_id',
                'items' => $companies
            ])
            <div class="form-group">
                {!! Form::label('corpid', '企业号ID',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('corpid', null, [
                        'class' => 'form-control',
                        'placeholder' => '(36个小写字母与阿拉伯数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'alphanum'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => isset($corp['enabled']) ? $corp['enabled'] : ''])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
