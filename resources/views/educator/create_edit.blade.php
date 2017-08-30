<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($tab['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $tab['id']]) }}
            @endif
            {{--<div class="form-group">--}}
            {{--{!! Form::label('user_id', '教职员工',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '教职员工',
                'id' => 'user_id',
                'items' => $users
            ])
            {{--<div class="form-group">--}}
            {{--<label for="team_ids" class="col-sm-4 control-label">所属组</label>--}}
            {{--<div class="col-sm-2">--}}
            {{--<select multiple name="team_ids[]" id="team_ids">--}}
            {{--@foreach($teams as $key => $value)--}}
            {{--@if(isset($selectedTeams))--}}
            {{--<option value="{{$key}}" @if(array_key_exists($key,$selectedTeams))selected="selected"@endif>--}}
            {{--{{$value}}--}}
            {{--</option>--}}
            {{--@else--}}
            {{--<option value="{{$key}}">{{$value}}</option>--}}
            {{--@endif--}}
            {{--@endforeach--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.multiple_select', [
                'label' => '所属组',
                'for' => 'team_ids',
                'items' => $teams,
                'selectedItems' => isset($selectedTeams) ? $selectedTeams:[]
            ])
            {{--<div class="form-group">--}}
            {{--{!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            <div class="form-group">
                {!! Form::label('sms_quote', '可用短信条数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('sms_quote', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $educator['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
