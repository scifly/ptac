<div class="box">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => true])
    </div>
    <div class="box-body">
        <div id="tree" class="col-md-12"></div>
    </div>
    @include('partials.form_overlay')
</div>