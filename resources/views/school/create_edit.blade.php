<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-4 control-label',
                ]) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'minlength' => 6,
                        'maxlength' => 255
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('address', '地址',[
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-3">
                    {!! Form::text('address', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'minlength' => 6,
                        'maxlength' => 255
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '类型',
                'id' => 'school_type_id',
                'items' => $schoolTypes
            ])
            @include('partials.single_select', [
                'label' => '所属企业',
                'id' => 'corp_id',
                'items' => $corps
            ])
            @include('partials.enabled', ['enabled' => $school['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
