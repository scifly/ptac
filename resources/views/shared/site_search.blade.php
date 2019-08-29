<form action="#" class="sidebar-form">
    <div class="input-group">
        {!! Form::text('q', null, [
            'placeholder' => '搜索...',
            'class' => 'form-control'
        ]) !!}
        <span class="input-group-btn">
            {!! Form::button(
                Html::tag('i', '', ['class' => 'fa fa-search']),
                ['name' => 'search', 'id' => 'search-btn', 'class' => 'btn btn-flat']
            ) !!}
        </span>
    </div>
</form>