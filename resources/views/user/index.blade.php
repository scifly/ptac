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
{!! Form::text('sn_0', null, ['class' => 'sn', 'maxlength' => 10]) !!}<br />
{!! Form::text('sn_1', null, ['class' => 'sn', 'maxlength' => 10]) !!}<br />
{!! Form::text('sn_2', null, ['class' => 'sn', 'maxlength' => 10]) !!}<br />
{!! Form::text('sn_3', null, ['class' => 'sn', 'maxlength' => 10]) !!}<br />
{{--{!! Form::submit() !!}--}}
{!! Form::close() !!}
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
{{--<script src="https://js.pusher.com/4.3/pusher.min.js"></script>--}}
<script>
    $('input').on('keyup', function() {
        var paths = $(this).attr('name').split('_'),
            i = paths[1];
        if ($(this).val().length === parseInt($(this).attr('maxlength'))) {
            console.log('return');
            $('input[name=sn_' + (i+1) + ']').focus();
        }
    });
</script>
</body>
</html>