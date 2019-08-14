@include('shared.recharge', [
    'model' => $educator,
    'formId' => 'formEducator',
    'name' => $educator->user->realname
])