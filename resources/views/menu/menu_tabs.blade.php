<ul class="todo-list ui-sortable">
    @foreach ($tabs as $tab)
        <li>
            <span class="handle">
                <i class="fa fa-ellipsis-v"></i>
                <i class="fa fa-ellipsis-v"></i>
            </span>
            <span id="{{ $tab->id }}" class="text">{{ $tab->name }}</span>
        </li>
    @endforeach
</ul>