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
{!! Form::text('sn', null, ['class' => 'sn']) !!}<br />
{!! Form::text('sn', null, ['class' => 'sn']) !!}<br />
{!! Form::text('sn', null, ['class' => 'sn']) !!}<br />
{!! Form::text('sn', null, ['class' => 'sn']) !!}<br />
{{--{!! Form::submit() !!}--}}
{!! Form::close() !!}
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
<script>
    var $inputs = $('.sn'),
    i = 0;

    $('input').on('change paste', function() {
        i++;
        $($inputs[i]).focus();
    });
</script>
</body>
</html>