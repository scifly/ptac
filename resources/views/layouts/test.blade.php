<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>test</title>
    <link rel="stylesheet" href="{{ URL::asset('Css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ URL::asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/test.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/skins/_all-skins.min.css') }}">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header"></header>
    <aside class="main-sidebar"></aside>
    <div class="content-wrapper">
        <section class="content-header"></section>
        <section class="content" id="testcontent">
            @if(isset($tabs))
                <div class="col-lg-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                        @foreach ($tabs as $tab)
                            <li @if($tab['active']) class="active" @endif>
                                <a href="#{{ $tab['id'] }}" data-toggle="tab" data-url="{{ $tab['url'] }}" class="tab">
                                    {{ $tab['name'] }}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach ($tabs as $tab)
                                <div class="@if($tab['active']) active @endif tab-pane" id="{{ $tab['id'] }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>
    <div class="control-sidebar-bg"></div>
</div>
<!-- jQuery 3 -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
<script src="{{ URL::asset('js/test/test.js') }}"></script>
</body>