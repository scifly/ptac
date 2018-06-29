<div class="modal fade" id="user-profile">
    <div class="modal-dialog">
        <div class="modal-content modal">
            {{--<div class="modal-header">--}}
            {{--</div>--}}
            <div class="modal-body with-border">
                {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>--}}
                @include('user.edit', [
                    'user' => Auth::user(),
                    'breadcrumb' => '个人信息'
                ])
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>