@if (!empty($tags))
    @include('shared.multiple_select', [
        'label' => '所属标签',
        'id' => 'tag_ids',
        'icon' => 'fa fa-tags',
        'items' => $tags,
        'selectedItems' => $selectedTags
    ])
@endif