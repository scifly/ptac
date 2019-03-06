<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pusher Test</title>
</head>
<body>
<h1>Pusher Test</h1>
<p>
    Try publishing an event to channel <code>my-channel</code>
    with event name <code>my-event</code>.
</p>
{!! Form::open(['url' => 'test/index', 'method' => 'post']) !!}
{!! Form::text('sn[]', null, ['class' => 'is']) !!}<br />
{!! Form::text('sn[]', null, ['class' => 'is']) !!}<br />
{!! Form::text('sn[]', null, ['class' => 'is']) !!}<br />
{!! Form::text('sn[]', null, ['class' => 'is']) !!}<br />
{{--{!! Form::submit() !!}--}}
{!! Form::close() !!}
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
<script>
    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;
    // var pusher = new Pusher('4e759473d69a97307905', {
    //     cluster: 'eu',
    //     encrypted: true
    // });
    // var channel = pusher.subscribe('my-channel');
    // channel.bind('my-event', function (data) {
    //     alert(JSON.stringify(data));
    // });
</script>
</body>
</html>