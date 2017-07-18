@extends('layouts.master')
@section('header') <h1>$title</h1> @endsection
@section('breadcrumb') $breadcrumb @endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#content1" data-toggle="tab">学校管理</a></li>
                    <li><a href="#content2" data-toggle="tab">类型设置</a></li>
                    <li><a href="#content3" data-toggle="tab">学期设置</a></li>
                    <li><a href="#content4" data-toggle="tab">教职员工组别设置</a></li>
                </ul>
                <div class="tab-content">
                    <!--学校管理-->
                    <div class="active tab-pane" id="content1">
                    </div>
                    <!--类型设置-->
                    <div class="tab-pane" id="content2">
                    </div>
                    <!--学期设置-->
                    <div class="tab-pane" id="content3">
                    </div>
                    <!--教职员工组别设置-->
                    <div class="tab-pane" id="content4">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
