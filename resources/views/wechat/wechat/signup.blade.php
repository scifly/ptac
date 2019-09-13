@extends('layouts.wap')
@section('title') {!! '注册' !!} @stop
@section('content')
<div id="container" class="cls-container">
    <div id="bg-overlay"></div>
    <div class="cls-content">
        <div class="cls-content-lg panel">
            <div class="panel-body">
                <div class="mar-ver pad-btm">
                    <h1 class="h3">注册</h1>
                    <p>请验证手机号码完成注册</p>
                </div>
                {!! Form::open(['method' => 'POST', 'id' => 'formSignup']) !!}
                {!! Form::hidden('openid', $openid) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::text('mobile', null, [
                                    'type' => 'tel',
                                    'class' => 'form-control',
                                    'placeholder' => '手机号码'
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="E-mail" name="email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Username" name="username">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>
                        </div>
                    </div>
                    <div class="checkbox pad-btm text-left">
                        <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox">
                        <label for="demo-form-checkbox">I agree with the <a href="#" class="btn-link text-bold">Terms and Conditions</a></label>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
                {!! Form::close() !!}
            </div>
            <div class="pad-all">
                Already have an account ? <a href="pages-login.html" class="btn-link mar-rgt text-bold">Sign In</a>

                <div class="media pad-top bord-top">
                    <div class="pull-right">
                        <a href="#" class="pad-rgt"><i class="demo-psi-facebook icon-lg text-primary"></i></a>
                        <a href="#" class="pad-rgt"><i class="demo-psi-twitter icon-lg text-info"></i></a>
                        <a href="#" class="pad-rgt"><i class="demo-psi-google-plus icon-lg text-danger"></i></a>
                    </div>
                    <div class="media-body text-left text-main text-bold">
                        Sign Up with
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="demo-bg">
        <div id="demo-bg-list">
            <div class="demo-loading"><i class="psi-repeat-2"></i></div>
            <img class="demo-chg-bg bg-trans active" src="img/bg-img/thumbs/bg-trns.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-1.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-2.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-3.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-4.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-5.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-6.jpg" alt="Background Image">
            <img class="demo-chg-bg" src="img/bg-img/thumbs/bg-img-7.jpg" alt="Background Image">
        </div>
    </div>
</div>
@stop
@section('script')
    {!! Html::script('/js/wechat/wechat/signup.js') !!}
@endsection