	<div class="row">
			<div class="title">
				{{ $className }}·班级成绩分析
			</div>
	        <div class="box-tools pull-right">
	            <i class="fa fa-close " id="close-data"></i>
	        </div>
		</div>
		<div class="row">
			<div class="subtitle">
				{{ $examName }}
			</div>
	        <table id="score-count" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
	            <thead>
					<tr class="bg-info">
		                <th>科目</th>
		                <th>统计人数</th>
		                <th>最高分</th>
		                <th>最低分</th>
		                <th>平均分</th>
		                <th>平均分以上</th>
		                <th>平均分以下</th>
		            </tr>
	            </thead>
	            <tbody>
					@foreach($oneData as $one)
					<tr>
						<td>{{ $one['sub'] }}</td>
						<td>{{ $one['count'] }}</td>
						<td>{{ $one['max'] }}</td>
						<td>{{ $one['min'] }}</td>
						<td>{{ $one['avg'] }}</td>
						<td>{{ $one['big_number'] }}</td>
						<td>{{ $one['min_number'] }}</td>
					</tr>
					@endforeach
	            </tbody>
	        </table>
		</div>
		
		<div class="row">
			<div class="subtitle">
				各科分数段成绩分布情况
			</div>
	        <table id="score-level" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
				@foreach($rangs as $ran)
	             <thead>
					<tr class="bg-info">
		                <th>科目</th>
		                <th>统计人数</th>
						@foreach($ran as $r)
		                <th>{{ $r['range']['min'] }}-{{ $r['range']['max'] }}</th>
						@endforeach
					</tr>
	            </thead>
	            <tbody>
				<tr>
					<td>{{ $ran[0]['score']['sub'] }}</td>
					<td>{{ $ran[0]['score']['count'] }}</td>
					@foreach($ran as $rs)
					<td>{{ $rs['score']['number'] }}</td>
					@endforeach
				</tr>
				</tbody>
				@endforeach
	        </table>
		</div>

		{{--<div class="row">--}}
			{{--<div class="subtitle">--}}
				{{--总分分数段成绩分布情况--}}
			{{--</div>--}}
	        {{--<table id="sumscore" style="width: 100%;"--}}
               {{--class="display nowrap table table-striped table-bordered table-hover table-condensed">--}}
	            {{--<thead>--}}
					{{--<tr class="bg-info">--}}
		                {{--<th>科目</th>--}}
		                {{--<th>统计人数</th>--}}
		                {{--<th>900分以上</th>--}}
		                {{--<th>825-900分</th>--}}
		                {{--<th>750-825分</th>--}}
		                {{--<th>675-750分</th>--}}
		                {{--<th>600-675分</th>--}}
		                {{--<th>525-600分</th>--}}
		                {{--<th>450-525分</th>--}}
		                {{--<th>450-375分</th>--}}
		                {{--<th>375-300分</th>--}}
		                {{--<th>300分以下</th>--}}
		            {{--</tr>--}}
	            {{--</thead>--}}
	            {{--<tbody>--}}
	            	{{--<tr>--}}
	            		{{--<td>总分</td>--}}
	            		{{--<td>58</td>--}}
	            		{{--<td>0</td>--}}
	            		{{--<td>0</td>--}}
	            		{{--<td>0</td>--}}
	            		{{--<td>0</td>--}}
	            		{{--<td>0</td>--}}
	            		{{--<td>7</td>--}}
	            		{{--<td>28</td>--}}
	            		{{--<td>18</td>--}}
	            		{{--<td>3</td>--}}
	            		{{--<td>2</td>--}}
	            	{{--</tr>--}}
	            {{--</tbody>--}}
	        {{--</table>--}}
		{{--</div>--}}
		{{--<div class="table-pie">--}}
		{{--</div>--}}
