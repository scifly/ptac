@isset($departmentUsers)
    @foreach($departmentUsers as $id=>$username)
        <li id="{{$id}}"><span class="handle ui-sortable-handle"><i class="fa fa-user"></i></span><span
                    class="text">{{$username}}</span>
            <div class="tools"><i class="fa fa-close close-node"></i><input type="hidden" value="6"></div>
        </li>
    @endforeach
@endisset
