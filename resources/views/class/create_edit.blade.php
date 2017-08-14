<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '4',
                        'data-parsley-maxlength' => '40'
                    ]) !!}
                </div>
                <div class="col-sm-5">
                    <p class="form-control-static text-danger">{{ $errors->first('name') }}</p>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('grade_id', '所属年级',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('grade_id', $grades, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('educator_ids', '年级主任',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    <select multiple="multiple" name="educator_ids[]" id="educator_ids">
                        @foreach($educators as $key => $value)
                            @if(isset($selectedEducators))
                                <option value="{{$key}}" @if(array_key_exists($key,$selectedEducators))selected="selected"@endif>
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
                {!! Form::label('enabled', '是否启用', [
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($squad['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>

        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            </div>
        </div>
    </div>
</div>
