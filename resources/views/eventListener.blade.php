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
<input type="hidden" id="userId" value="{{ $user->id }}"/>
<div id="app"><p>广播时间侦听</p></div>
<script src="js/app.js" charset="utf-8"></script>
</body>
</html>