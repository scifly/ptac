<div class="row">
    <!--表单和datatables-->
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header" style="border-bottom: 1px dashed #ccc">
                <!--表单-->
                <form id="form" class="form-inline">
                    <div class="form-group" style="margin-right: 50px">
                        <label class="control-label">请选择统计范围：</label>
                        <div class="form-control" style="border: none;">
                            <input type="radio" name="type" id="grade" value="grade" checked>
                            <label for="grade" style="margin-right: 5px">年级</label>
                            <input type="radio" name="type" id="class" value="class">
                            <label for="class">班级</label>
                            {!! Form::radio('type', 'grade', true, ['id' => 'grade']) !!}
                            {!! Form::label('grade', '年级', ['style' => 'margin-right: 5px;']) !!}
                            {!! Form::radio('type', 'class', true, ['id' => 'class']) !!}
                            {!! Form::label('class', '班级') !!}
                        </div>
                    </div>
                    <div class="form-group grade_div" style="margin-right: 50px">
                        <label class="control-label" for="grade_id">请选择年级：</label>
                        {!! Form::label('grade_id', '请选择年级：', ['class' => 'control-label']) !!}
                        {!! Form::select('grade_id', $grades, null, [
                            'id' => 'grade_id',
                            'class' => 'form-control text-blue',
                            'style'=>'margin-right: 10px'
                        ]) !!}
                    </div>
                    <div class="form-group class_div" style="margin-right: 50px;display: none">
                        {!! Form::label('class_id', '请选择班级：', ['class' => 'control-label']) !!}
                        {!! Form::select('class_id', $classes, null, [
                            'id' => 'class_id',
                            'class' => 'form-control text-blue',
                            'style'=>'margin-right: 10px'
                        ]) !!}
                    </div>
                    <div class="form-group" style="margin-right: 50px">
                        <label class="control-label" for="exam_id">请选择考试：</label>
                        {!! Form::label('exam_id', '请选择考试：', ['class' => 'control-label']) !!}
                        {!! Form::select('exam_id', $exams, null, [
                            'id' => 'exam_id',
                            'class' => 'form-control text-blue',
                            'style'=>'margin-right: 10px'
                        ]) !!}
                    </div>
                    <div class="form-group">
                        <a id="submit" class="btn btn-primary">提交</a>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <!--datatables-->
                <table id="data-table" style="width: 100%"
                       class="display nowrap table table-striped table-bordered table-hover table-condensed">
                    <thead>
                    <tr class="bg-info">
                        <th>#</th>
                        <th>分数段</th>
                        <th>计入统计数</th>
                        <th>所占百分比（%）</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--chart-->
    <div class="col-xs-12 chart" style="display: none">
        <div class="box box-primary">
            <div class="box-body">
                <div id="barChart" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>