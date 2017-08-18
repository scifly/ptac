<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <a href="javascript:" class="btn btn-primary">
                    <i class="fa fa-mail-reply"></i>
                    返回列表
                </a>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('name', 'Action名称', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('name', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入功能名称)',
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '80'
                            ]) !!}

                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('method', '方法名称', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('method', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入方法名称)',
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '255',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('route', '路由', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('route', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入路由)',
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '255',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('controller', '控制器名称',[
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('controller', null, [
                                'class' => 'form-control  special-form-control',
                                'placeholder' => '(请输入控制器名称)',
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('view', 'view路径', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('view', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入view路径)',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('js', 'js文件路径', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('js', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入js文件路径)',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('remark', '备注', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('remark', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入备注)',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">使用插件</label>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td>
                                        <label for="datatable">
                                            <span class="badge bg-light-blue">datatable</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label for="parsley">
                                            <span class="badge bg-light-blue">parsley</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label for="select2">
                                            <span class="badge bg-light-blue">select2</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label for="chart">
                                            <span class="badge bg-light-blue">chart</span>
                                        </label>
                                    </td>
                                    <td>
                                        <label for="map">
                                            <span class="badge bg-light-blue">map</span>
                                        </label>
                                    </td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input id="datatable" type="checkbox" name="datatable"
                                               @if(!empty($action['datatable'])) checked @endif
                                               data-render="switchery" data-theme="default"
                                               data-switchery="true" data-classname="switchery switchery-small"/>
                                    </td>
                                    <td>
                                        <input id="parsley" type="checkbox" name="parsley"
                                               @if(!empty($action['parsley'])) checked @endif
                                               data-render="switchery" data-theme="default"
                                               data-switchery="true" data-classname="switchery switchery-small"/>
                                    </td>
                                    <td>
                                        <input id="select2" type="checkbox" name="select2"
                                               @if(!empty($action['select2'])) checked @endif
                                               data-render="switchery" data-theme="default"
                                               data-switchery="true" data-classname="switchery switchery-small"/>
                                    </td>
                                    <td>
                                        <input id="chart" type="checkbox" name="chart"
                                               @if(!empty($action['chart'])) checked @endif
                                               data-render="switchery" data-theme="default"
                                               data-switchery="true" data-classname="switchery switchery-small"/>
                                    </td>
                                    <td>
                                        <input id="map" type="checkbox" name="map"
                                               @if(!empty($action['map'])) checked @endif
                                               data-render="switchery" data-theme="default"
                                               data-switchery="true" data-classname="switchery switchery-small"/>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('action_type_ids', 'HTTP请求类型', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-9">
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <select name="action_type_ids[]" id="action_type_ids" multiple class="col-sm-3">
                                @foreach($actionTypes as $key => $value)
                                    @if(isset($selectedActionTypes))
                                        <option value="{{$key}}"
                                                @if(array_key_exists($key, $selectedActionTypes))
                                                    selected
                                                @endif
                                        >
                                            {{$value}}
                                        </option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="enabled" class="col-sm-3 control-label">
                            是否启用
                        </label>
                        <div class="col-sm-6" style="margin-top: 5px;">
                            <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                                   data-theme="default" data-switchery="true"
                                   @if(!empty($action['enabled'])) checked @endif
                                   data-classname="switchery switchery-small"/>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.form.buttons')
        </div>
    </div>
</div>
