<div class="box box-primary">
    <div class="box-header"></div>
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
            <div class="form-group">
                {!! Form::label('school_type_id', '类型',[
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_type_id', $schoolTypes, null, [
                        'class' => 'form-control'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('corp_id', '所属企业',[
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-2">
                    {!! Form::select('corp_id', $corps, null, [
                        'class' => 'form-control'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-3 control-label">
                    是否启用
                </label>
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($school['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
