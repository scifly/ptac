<div style="position: relative;">
    <label class="weui-cell weui-check__label" id="{{ $type }}-{{ $target->id }}"
           data-item="{{ $target->id }}" data-uid="{{ $target->id }}"
           data-type="{{ $type }}">
        <div class="weui-cell__hd">
            {!! Form::checkbox('targets[]', 0, null, [
                'id' => 'targets[]',
                'class' => 'weui-check target-check'
            ]) !!}
            <i class="weui-icon-checked"></i>
        </div>
        <div class="weui-cell__bd">
            {{--<img src="{{ $type == 'department' ? asset('img/department.png') : asset('img/personal.png') }}"--}}
                 {{--@if ($type == 'department') style="border-radius: 0;" @endif--}}
                 {{--class="js-go-detail lazy" width="75" height="75">--}}
            <span class="contacts-text">
                {{ $type == 'department' ? $target->name : $target->realname . ' - '. $target->mobiles->first()->mobile }}
            </span>
        </div>
    </label>
    @if ($type == 'department')
        <a class="icon iconfont icon-jiantouyou expand targets"></a>
    @endif
</div>