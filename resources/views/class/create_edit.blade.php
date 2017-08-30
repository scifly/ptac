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
            {{--<div class="form-group">--}}
            {{--{!! Form::label('grade_id', '所属年级',['class' => 'col-sm-4 control-label']) !!}--}}
            {{--<div class="col-sm-2">--}}
            {{--{!! Form::select('grade_id', $grades, null, ['class' => 'form-control']) !!}--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades
            ])
            <div class="form-group">
                <label for="educator_ids" class="col-sm-4 control-label">年级主任</label>
                <div class="col-sm-3">
                    <select multiple name="educator_ids[]" id="educator_ids">
                        @foreach($educators as $key => $value)
                            @if(isset($selectedEducators))
                                <option value="{{$key}}"
                                        @if(array_key_exists($key,$selectedEducators))selected="selected"@endif>
                                    {{$value}}
                                </option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @include('partials.multiple_select', [
                'label' => '年级主任',
                'for' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => isset($selectedEducators) ? $selectedEducators:[]
            ])
            @include('partials.enabled', ['enabled' => $class['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
