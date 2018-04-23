<!--content-->
@include('partials.modal_dialog')
{{--@yield('content')--}}
@if(!empty($tabs))
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                @foreach ($tabs as $tab)
                    <li @if($tab['active']) class="active" @endif>
                        <a href="#{{ $tab['id'] }}"
                           data-toggle="tab"
                           data-uri="{{ $tab['url'] }}"
                           class="tab @if($tab['active']) text-blue @else text-gray @endif"
                        >
                            @if(isset($tab['icon']))
                                <i class="{{ $tab['icon'] }}" style="width: 20px;"></i>
                            @else
                                <i class="fa fa-circle-o" style="width: 20px;"></i>
                            @endif
                            {{ $tab['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach ($tabs as $tab)
                    <div class="@if($tab['active']) active @endif tab-pane card" id="{{ $tab['id'] }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    {!! $content !!}
@endif