<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no, email=no">
    <meta name="HandheldFriendly" content="true">
    @yield('title')
    <link rel="stylesheet" href="{{ asset('/css/demo/frozenui.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/demo/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/demo/eucp.css') }}">
</head>
<body ontouchstart>
<section class="ui-container">
    <div class="index-wrap">
        <section id="list">
            <table class="ui-table ui-border">
                <tbody class="eucp-block">
                @yield('content')
                </tbody>
            </table>
        </section>
    </div>
</section>
<script src="{{ asset('/js/demo/lib/zepto.min.js') }}"></script>
<script src="{{ asset('/js/demo/index.js') }}"></script>
<script>
    $(".eucp-jiugong-icon").each(function(){
        $(this).css(
            "background-image",
            "url(../img/demo/" + $(this).data('img') + ".png)"
        );
    });
</script>
</body>
</html>