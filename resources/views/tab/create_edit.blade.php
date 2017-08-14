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
                        {!! Form::label('name', 'Action名称',[
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('name', null, [
                                'class' => 'form-control special-form-control',
                                'placeholder' => '(请输入卡片名称)',
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '80'
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
                                'data-parsley-required' => 'true',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="icon_id" class="col-sm-3 control-label">
                            图标
                        </label>
                        <div class="col-sm-6"
                             style="
                                overflow-y: scroll;
                                height: 200px;
                                border: 1px solid gray;
                                margin-left: 15px;
                                width: 393px;
                            "
                        >
                            @foreach($icons as $group => $_icons)
                                @foreach ($_icons as $key => $value)
                                    <label for="icon_id">
                                        <input id="icon_id" type="radio" name="icon_id"
                                               value="{{ $key }}" class="minimal"
                                               @if(isset($menu) && $menu['icon_id'] == $key)
                                               checked
                                                @endif
                                        >
                                    </label>
                                    <i class="{{ $value }}" style="margin-left: 10px;">&nbsp; {{ $value }}</i><br />
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="enabled" class="col-sm-3 control-label">是否启用</label>
                        <div class="col-sm-6" style="margin-top: 5px;">
                            <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                                   data-theme="default" data-switchery="true"
                                   @if(!empty($tab['enabled'])) checked @endif
                                   data-classname="switchery switchery-small"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                {{--button--}}
                <div class="form-group">
                    <div class="col-sm-3 col-sm-offset-3">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left','id' =>'save']) !!}
                        {!! Form::reset('取消', ['class' => 'btn btn-default pull-right','id' =>'cancel']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
