<div class="modal fade" id="user-profile">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                {{--<h4 class="modal-title">个人信息</h4>--}}
            </div>
            <div class="modal-body with-border">
                @include('user.edit', [
                    'user' => Auth::user(),
                    'mobile' => '12345678901',
                    'breadcrumb' => '个人信息'
                ])
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>