	<div class="row">
		<div class="title">
			@if(count($student) != 0)
			{{ $student->user->realname }}学生成绩统计
			@else
				学生成绩统计
			@endif
		</div>
        <div class="box-tools pull-right">
            <i class="fa fa-close " id="close-data"></i>
        </div>
		
	</div>
	
	<div class="row">
		<div class="subtitle">
			@if(count($student) != 0)
				{{ $student->user->realname }}同学考试情况
			@else
				考试情况
			@endif
		</div>
        <table id="scores" style="width: 100%;"
           class="display nowrap table table-striped table-bordered table-hover table-condensed">
			<thead>
				<tr class="bg-info">
	                <th>序号</th>
	                <th>考试名称</th>
	                <th>考试时间</th>
					@if(!empty($subjectName))
	                @foreach($subjectName as $key => $name)
					<th class="subjectName"> {{$name}} </th>
	                <th>班排</th>
	                <th>年排</th>
					@endforeach
	                <th class="subjectName">总分</th>
	                <th>班排</th>
	                <th>年排</th>
					@endif
				</tr>
            </thead>
            <tbody>
			@if(!empty($examScore))
				@foreach($examScore as $exam)
				<tr>
					<td>{{ $exam['examId'] }}</td>
					<td class="testName">{{ $exam['examName'] }}/td>
					<td>{{ $exam['examTime'] }}</td>
					@foreach($exam['score'] as $item)
					<td>{{ $item['score'] }}</td>
					<td class="classrankeItem">{{ $item['class_rank'] }}</td>
					<td class="graderankeItem">{{ $item['grade_rank'] }}</td>
					@endforeach
						<td>250</td>
					<td class="classrankeItem">18</td>
					<td class="graderankeItem">153</td>
				</tr>
				@endforeach
				@endif
            </tbody>
        </table>
	</div>
	
	<div class="row">
		<div class="subtitle">
			徐邹昊各科班级排名变化
		</div>
		<div id="classranke">
			
		</div>
	</div>
	
	<div class="row">
		<div class="subtitle">
			徐邹昊各科班级排名变化
		</div>
		<div id="graderanke">
			
		</div>
	</div>