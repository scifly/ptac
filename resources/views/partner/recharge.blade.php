@include('shared.recharge', [
    'model' => $educator,
    'formId' => 'formPartner',
    'name' => $educator->user->realname
])