@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a id="exampreview" class="btn btn-primary pull-right">
                成绩预览
            </a>
        </div>
        <div class="btn-group">
            <a id="examsend" class="btn btn-primary pull-right">
                成绩发送
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    成绩管理/成绩发送
@endsection
@section('content')

    学校:
    <select name="school" id="school">
        <option value="0"><--请选择学校--></option>
        @if(isset($schools))
            @foreach($schools as $school)
                <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach
        @endif
    </select>
    年级:
    <select name="grade">
        <option value="0"><--请选择年级--></option>
    </select>
    班级:
    <select name="class">
        <option value="0"><--请选择班级--></option>
    </select>


    考次:
    <select name="exam">
        <option value="0"><--请选择考次--></option>
    </select>

    <div class="panel panel-default" id="panel">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">科目</h3>
            </div>
            <div class="panel-body" id="exam_panel">
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">发布项目</h3>
            </div>
            <div class="panel-body">
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="0">班排名
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="1">年排名
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="2">班平均
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="3">年平均
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="4">班最高
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="5">年最高
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="6">班最低
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="7">年最低
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="8">科目总分
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" name="project" value="9">及格分
                </label>
            </div>
        </div>
    </div>



    <div class="panel-body">
        <div class="table-responsive">
            <table id="table" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0"
                   width="100%">
                <thead>
			<tr class="bg-info">
                    <th></th>
                    <th>姓名</th>
                    <th>内容</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

