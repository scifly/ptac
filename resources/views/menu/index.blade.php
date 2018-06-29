<div class="box box-default box-solid">
    <div class="box-header with-border">
        <span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
        <div class="box-tools pull-right">
            @if (isset($buttons))
                @foreach ($buttons as $button)
                    @can('act', $uris[$button['id']])
                        <button id="{{ $button['id'] }}" type="button" class="btn btn-box-tool">
                            <i class="{{ $button['icon'] }} text-blue"> {{ $button['label'] }}</i>
                        </button>
                    @endcan
                @endforeach
            @endif
        </div>
    </div>
    <div class="box-body">
        <div id="tree" class="col-md-12"></div>
    </div>
    @include('partials.form_overlay')
</div>