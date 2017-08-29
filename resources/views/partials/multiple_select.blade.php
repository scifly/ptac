<div class="form-group">
    <label for="{{ $for }}" class="col-sm-3 control-label">
        {{ $label }}
    </label>
    <div class="col-sm-6">
        <select multiple name="{{ $for }}[]" id="{{ $for }}" style="width: 100%;">
            @foreach ($items as $key => $value)
                @if(isset($selectedItems))
                    <option value="{{ $key }}"
                            @if(array_key_exists($key, $selectedItems))
                            selected
                            @endif
                    >
                        {{ $value }}
                    </option>
                @else
                    <option value="{{ $key }}">{{ $value }}</option>
                @endif
            @endforeach
        </select>
    </div>
</div>