<div class="form-group">
    {!! Form::label($id, $label, [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="{!! $icon ?? 'fa fa-list-alt' !!}" style="width: 20px;"></i>
            </div>
            {!! Form::select(
                $id . '[]', $items,
                isset($selectedItems) ? array_keys($selectedItems) : null,
                [
                    'id' => $id,
                    'multiple' => 'multiple',
                    'class' => 'form-control',
                    'style' => 'width: 100%;',
                    isset($required) ? 'required' : '',
                ]
            ) !!}
{{--            <select multiple="multiple" name="{{ $id }}[]" id="{{ $id }}"--}}
{{--                    class='form-control select2' style="width: 100%;"--}}
{{--                    {!! isset($required) ? 'required' : '' !!}--}}
{{--            >--}}
{{--                @foreach ($items as $key => $value)--}}
{{--                    @if (isset($selectedItems))--}}
{{--                        <option value="{{ $key }}"--}}
{{--                                @if(array_key_exists($key, $selectedItems))--}}
{{--                                selected--}}
{{--                                @endif--}}
{{--                        >--}}
{{--                            {{ $value }}--}}
{{--                        </option>--}}
{{--                    @else--}}
{{--                        <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                    @endif--}}
{{--                @endforeach--}}
{{--            </select>--}}
        </div>
    </div>
</div>