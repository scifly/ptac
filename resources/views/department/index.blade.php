<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        {{--<div id="jstree-department" class="col-md-12"></div>
        <div id="form_container" class="col-md-12" style="display:none;">
            <!-- create/edit form goes here -->
        </div>--}}
        <div id="tree" class="col-md-12"></div>
    </div>
    @include('partials.form_overlay')
</div>