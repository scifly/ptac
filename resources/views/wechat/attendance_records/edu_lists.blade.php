<div class="list list-normal">
	@foreach($normallist as $normal)
	<div class="list-item">
		<div class="list-item-info">
			<div class="username">姓名 : <span>{{ $normal['username'] }}</span></div>
			@foreach($normal['cusname'] as $cusname)
			<div class="parent">监护人 : <span>{{ $cusname }}</span></div>
			@endforeach
			@foreach($normal['cusphone'] as $cusphone)
			<div class="mobile">手机 : <span>{{ $cusphone }}</span></div>
			@endforeach
			<div class="otherinfo">打卡时间: {{ $normal['punch_time'] }}</div>
		</div>
	</div>
	@endforeach
</div>
<div class="list list-abnormal">
	@foreach($abnormallist as $abnormal)
		<div class="list-item">
			<div class="list-item-info">
				<div class="username">姓名 : <span>{{ $abnormal['username'] }}</span></div>
				@foreach($abnormal['cusname'] as $cusname)
					<div class="parent">监护人 : <span>{{ $cusname }}</span></div>
				@endforeach
				@foreach($abnormal['cusphone'] as $cusphone)
					<div class="mobile">手机 : <span>{{ $cusphone }}</span></div>
				@endforeach
				<div class="otherinfo">打卡时间: {{ $abnormal['punch_time'] }}</div>
			</div>
		</div>
	@endforeach
</div>
<div class="list list-norecords">
	@foreach($nostulist as $nostu)
		<div class="list-item">
			<div class="list-item-info">
				<div class="username">姓名 : <span>{{ $nostu['username'] }}</span></div>
				@foreach($nostu['cusname'] as $cusname)
					<div class="parent">监护人 : <span>{{ $cusname }}</span></div>
				@endforeach
				@foreach($nostu['cusphone'] as $cusphone)
					<div class="mobile">手机 : <span>{{ $cusphone }}</span></div>
				@endforeach
			</div>
		</div>
	@endforeach
</div>