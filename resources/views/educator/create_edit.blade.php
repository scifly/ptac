<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tab['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $tab['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('user_id', '教职员工',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="team_ids" class="col-sm-4 control-label">所属组</label>
                <div class="col-sm-2">
                    <select multiple name="team_ids[]" id="team_ids">
                        @foreach($teams as $key => $value)
                            @if(isset($selectedTeams))
                                <option value="{{$key}}" @if(array_key_exists($key,$selectedTeams))selected="selected"@endif>
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
                {!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('sms_quote', '可用短信条数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('sms_quote', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="enabled" class="col-sm-3 control-label">
                    是否启用
                </label>
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($educator['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
