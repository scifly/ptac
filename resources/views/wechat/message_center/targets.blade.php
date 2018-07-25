@foreach($targets as $target)
    <div style="position: relative;">
        <label class="weui-cell weui-check__label" id="{!! $type !!}-{!! $target->id !!}"
               data-item="{!! $target->id !!}" data-uid="{!! $target->id !!}"
               data-type="{!! $type !!}">
            <div class="weui-cell__hd">
                {!! Form::checkbox(
                    'targets[]', 0,
                    isset($selectedTargetIds) ? in_array($target->id, $selectedTargetIds) : null,
                    [
                        'id' => 'targets[]',
                        'class' => 'weui-check target-check'
                    ]
                ) !!}
                <i class="weui-icon-checked"></i>
            </div>
            <div class="weui-cell__bd clearfix">
                <img src="{!! $type == 'department' ? asset('img/department.png') : asset('img/personal.png') !!}"
                     @if ($type == 'department') style="border-radius: 0;" @endif
                     class="js-go-detail lazy target-image" width="25" height="25">
                <span class="contacts-text">
                {!!
                    $type == 'department'
                        ? $target->name
                        : $target->realname . ' - '. $target->mobiles->where('isdefault', 1)->first()->mobile
                !!}
            </span>
            </div>
        </label>
        @if ($type == 'department')
            <a class="icon iconfont icon-jiantouyou expand targets"></a>
        @endif
    </div>
@endforeach