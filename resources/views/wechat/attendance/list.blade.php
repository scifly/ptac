<div class="list list-normal">
	@foreach($normals as $normal)
	<div class="list-item">
		<div class="list-item-info">
			<div class="username">姓名 : <span>{{ $normal['student'] }}</span></div>
			@foreach($normal['custodians'] as $custodian)
				<div class="parent">监护人 : <span>{{ $custodian }}</span></div>
			@endforeach
			@foreach($normal['mobiles'] as $mobile)
				<div class="mobile">手机 : <span>{{ $mobile }}</span></div>
			@endforeach
			<div class="otherinfo">打卡时间: {{ $normal['punch_time'] }}</div>
		</div>
	</div>
	@endforeach
</div>
<div class="list list-abnormal">
	@foreach($abnormals as $abnormal)
		<div class="list-item">
			<div class="list-item-info">
				<div class="username">姓名 : <span>{{ $abnormal['student'] }}</span></div>
				@foreach($abnormal['custodians'] as $custodian)
					<div class="parent">监护人 : <span>{{ $custodian }}</span></div>
				@endforeach
				@foreach($abnormal['mobiles'] as $mobile)
					<div class="mobile">手机 : <span>{{ $mobile }}</span></div>
				@endforeach
				<div class="otherinfo">打卡时间: {{ $abnormal['punch_time'] }}</div>
			</div>
		</div>
	@endforeach
</div>
<div class="list list-norecords">
	@foreach($missed as $m)
		<div class="list-item">
			<div class="list-item-info">
				<div class="username">姓名 : <span>{{ $m['student'] }}</span></div>
				@foreach($m['custodians'] as $custodian)
					<div class="parent">监护人 : <span>{{ $cusname }}</span></div>
				@endforeach
				@foreach($m['mobiles'] as $mobile)
					<div class="mobile">手机 : <span>{{ $mobile }}</span></div>
				@endforeach
			</div>
		</div>
	@endforeach
</div>