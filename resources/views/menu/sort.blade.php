<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header', [ 'buttons' => [
            'sort' => [
                'id' => 'sort',
                'label' => '保存排序',
                'icon' => 'fa fa-save'
            ]
        ]])
    </div>
    <div class="box-body">
        {!! Form::hidden('menuId', $menuId, ['id' => 'menuId']) !!}
        <ul class="todo-list ui-sortable">
            @foreach ($tabs as $tab)
                <li>
                    <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <span id="{!! $tab->id !!}" class="text">
                        {!! $tab->name !!}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
    @include('partials.form_overlay')
</div>