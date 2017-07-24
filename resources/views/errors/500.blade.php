@extends('layouts.master')
@section('content')
    <div class="error-page">
        <h2 class="headline text-red">500</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-red"></i> 服务器内部错误</h3>
            <p>
                服务器开小差了······您可以<a href="#">返回首页</a>看看 或者搜索你想要的内容。
            </p>
            <form class="search-form">
                <div class="input-group">
                    {{--<input type="text" name="search" class="form-control" placeholder="搜索">--}}
                    {!! Form::text('search'), null, [
                    'class' => 'form-control' ,
                    'placeholder' => '搜索'
                    ] !!}
                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-danger btn-flat"><i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection