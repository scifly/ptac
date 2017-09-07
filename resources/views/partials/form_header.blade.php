<span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
<div class="box-tools pull-right">
    @if(isset($buttons))
        @foreach($buttons as $button)
            {!! $button['html'] !!}
        @endforeach
    @endif
    <button id="record-list" type="button" class="btn btn-box-tool">
        <i class="fa fa-mail-reply text-blue"> 返回列表</i>
    </button>
</div>