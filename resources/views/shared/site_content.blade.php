<!--content-->
@include('shared.modal_delete')
{{--@yield('content')--}}
@if(!empty($tabs))
    <div class="box box-default box-solid">
        <div class="nav-tabs-custom" style="margin-bottom: 0;">
            <ul class="nav nav-tabs">
                @foreach ($tabs as $tab)
                    <li @if($tab['active']) class="active" @endif>
                        <a href="#{{ $tab['id'] }}" data-toggle="tab" data-uri="{{ $tab['url'] }}"
                           class="tab @if ($tab['active']) text-blue @else text-gray @endif"
                        >
                            <i class="{{ $tab['icon'] ?? 'fa fa-calendar-check-o' }}" style="width: 20px;"></i>
                            {{ $tab['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach ($tabs as $tab)
                    <div class="@if ($tab['active']) active @endif tab-pane card" id="{{ $tab['id'] }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    {!! $content !!}
@endif