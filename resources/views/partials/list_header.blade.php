<span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
@if(!isset($addBtn))
    <div class="box-tools pull-right">
        <button id="add-record" type="button" class="btn btn-box-tool">
            <i class="fa fa-plus text-blue"> 新增</i>
        </button>
        @if(isset($buttons))
            @foreach($buttons as $button)
                <button id="{{$button['id']}}" type="button" class="btn btn-box-tool">
                    <i class="{{$button['icon']}} text-blue"> {{$button['label']}}</i>
                </button>
            @endforeach
        @endif
    </div>
@endif


