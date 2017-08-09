@extends('layouts.master')
@section('header')
    <h1>成绩显示</h1>
@endsection
@section('content')
    <div class="row">
        <!--表单和datatables-->
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header" style="border-bottom: 1px dashed #ccc">
                    <!--表单-->
                    <form action="#" class="form-inline">
                        <div class="form-group" style="margin-right: 50px">
                            <label class="control-label">请选择统计范围：</label>
                            <div class="form-control" style="border: none;">
                                <input type="radio" name="choose" id="grade" value="grade" checked>
                                <label for="grade">年级</label>
                                <input type="radio" name="choose" value="class" id="school">
                                <label for="school">班级</label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-right: 50px">
                            <label class="control-label" for="select1">请选择班级/年级：</label>

                            {!! Form::select('id', $grades, null, ['class' => 'form-control']) !!}
                            {{--<select name="" id="select1" class="form-control" style="margin-right: 10px">--}}
                                {{--<option value="1">1</option>--}}
                                {{--<option value="2">2</option>--}}
                                {{--<option value="3">3</option>--}}
                            {{--</select>--}}
                        </div>
                        <div class="form-group" style="margin-right: 50px">
                            <label class="control-label" for="select2">请选择考试：</label>
                            <select name="" id="select2" class="form-control">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">提交</button>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <!--datatables-->
                    <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th>姓名</th>
                            <th>年级</th>
                            <th>班级</th>
                            <th>分数</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>张三</td>
                            <td>七年级</td>
                            <td>三班</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <td>张三</td>
                            <td>七年级</td>
                            <td>三班</td>
                            <td>99</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--chart-->
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    %
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="barChart" style="height:230px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
