<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" id="csrf_token">
    {!! Html::style('js/plugins/select2/css/select2.min.css') !!}
</head>
<body>
<h1>Pusher Test</h1>
<p>
    同步美视企业微信通讯录到本地数据库
</p>
{!! Form::open([
    'url' => 'test/index',
    'method' => 'post',
    'id' => 'testform',
    'enctype' => 'multipart/form-data'
]) !!}
{!! Form::select('test', [], null, ['style' => 'width: 200px;']) !!}
{!! Form::text('option[]', null, ['class' => 'test']) !!}
{!! Form::text('option[]', null, ['class' => 'test']) !!}
{!! Form::text('option[]', null, ['class' => 'test']) !!}
{!! Form::textarea('message', null) !!}
{!! Form::file('abc', ['id' => 'abc']) !!}
{!! Form::file('def', ['id' => 'def']) !!}
{!! Form::submit() !!}
{!! Form::close() !!}
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/pusher.min.js') }}"></script>
<script src="https://js.pusher.com/4.3/pusher.min.js"></script>
{!! Html::script('js/plugins/select2/js/select2.full.min.js') !!}
{!! Html::script('js/plugins/select2/js/i18n/zh-CN.js') !!}
<script>
    $('select[name=test]').select2({
        ajax: {
            url: 'index',
            dataType: 'json'
        },
        minimumInputLength: 1,
        language: 'zh-CN'
    });
    // Pusher.logToConsole = true;
    // var pusher = new Pusher('4e759473d69a97307905', {
    //         cluster: 'eu',
    //         encrypted: true
    //     }),
    //     channel = pusher.subscribe('my-channel');
    //
    // channel.bind('my-event', function (data) {
    //     $('textarea').append(data['message'] + "\n");
    // });
    // $('input[type="submit"]').on('click', function () {
    //     $.ajax({
    //         type: 'POST',
    //         url: 'index',
    //         dataType: 'json',
    //         data: {
    //             _token: $('#csrf_token').attr('content'),
    //             data: $('#testform').serialize()
    //         }
    //     });
    //     return false;
    // });
</script>
</body>
</html>