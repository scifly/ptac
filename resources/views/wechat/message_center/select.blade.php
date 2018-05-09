@foreach($departments as $department)
    @include('wechat.message_center.target', [
        'type' => 'department',
        'target' => $department
    ])
@endforeach
@foreach($gradeDepts as $department)
    @include('wechat.message_center.target', [
        'type' => 'department',
        'target' => $department
    ])
@endforeach
@foreach($classDepts as $department)
    @include('wechat.message_center.target', [
        'type' => 'department',
        'target' => $department
    ])
@endforeach
@foreach($users as $user)
    @include('wechat.message_center.target', [
        'type' => 'user',
        'target' => $user
    ])
@endforeach