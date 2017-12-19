<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header', [
            'buttons' => [
                ['html' => '<button id="save-rank" type="button" class="btn btn-box-tool"><i class="fa fa-save text-blue"> 保存排序</i></button>']
            ]
        ])
    </div>
</div>
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