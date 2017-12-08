<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
</head>
<body>
<div id="app">
    <p>
        This is the Event Listener page and when the event is fired off, this page will listen to the status update, and
        fire off the related listener command.
    </p>
</div>
<script src="js/app.js" charset="utf-8"></script>
</body>
</html>