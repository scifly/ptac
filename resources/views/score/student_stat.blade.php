<div class="row">
    <div class="title">
        {{ $student ? $student->user->realname : '' }}学生成绩统计
    </div>
    <div class="box-tools pull-right">
        <i class="fa fa-close " id="close-data"></i>
    </div>
</div>
<div class="row">
    <div class="subject-title">
        {{ $student ? $student->user->realname . '同学' : '' }}考试情况
    </div>
    {!! Form::hidden('subject-quantity', count($subjects), [
        'id' => 'subject-quantity',
        'class' => 'number',
        'title' => '科目数量',
    ]) !!}
    <table id="scores" style="width: 100%;"
           class="display nowrap table table-striped table-bordered table-hover table-condensed">
        <thead>
        <tr class="bg-info">
            <th>序号</th>
            <th>考试名称</th>
            <th>考试时间</th>
            @foreach ($subjecs as $id => $value)
                <th class="subject-name">{{ $value }}</th>
                <th>班排</th>
                <th>年排</th>
            @endforeach
            <th class="subjectName">总分</th>
            <th>班排</th>
            <th>年排</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($examScores as $examScore)
                <tr>
                    <td>{{ $examScore['examId'] }}</td>
                    <td class="exam-name">{{ $examScore['examName'] }}</td>
                    <td>{{ $examScore['examTime'] }}</td>
                    @foreach ($examScore['scores'] as $score)
                        <td>{{ $score['score'] }}</td>
                        <td class="class-rank">{{ $score['class_rank'] }}</td>
                        <td class="grade-rank">{{ $score['grade_rank'] }}</td>
                    @endforeach
                    <td>{{ $examScore['examTotal']['score'] }}</td>
                    <td class="class-rank">{{ $examScore['examTotal']['class_rank'] }}</td>
                    <td class="grade-rank">{{ $examScore['examTotal']['grade_rank'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="row">
    <div class="subject-title">
        {{ $student ? $student->user->realname : ''}}各科班级排名变化
    </div>
    <div id="class-rank"></div>
</div>
<div class="row">
    <div class="subject-title">
        {{ $student ? $student->user->realname : ''}}各科年级排名变化
    </div>
    <div id="grade-rank"></div>
</div>