@extends('layouts.master')
@section('content')
    <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> 页面未找到</h3>
            <p>
                抱歉！页面好像去火星了~
                你可以<a href="#">返回首页</a> 或者搜索你想要的内容。
            </p>
            <form class="search-form">
                <div class="input-group">
                    {!! Form::text('search'), null, [
                    'class' => 'form-control' ,
                    'placeholder' => '搜索'
                    ] !!}
                    <div class="input-group-btn">
                        <button type="submit" name="submit" class="btn btn-warning btn-flat"><i
                                    class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection