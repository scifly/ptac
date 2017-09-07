<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Draggable Events</h4>
                    </div>
                    <div class="box box-primary" style="position: relative; left: 0px; top: 0px;">
                        <div class="box-body">
                            @if (!empty($userId))
                                <input hidden name="user_id" value={{$userId}}>
                            @endif
                            <input hidden name="isAdmin" value={{$isAdmin}}>
                            <ul id="external-events" class="todo-list ui-sortable" style="overflow: visible">
                                @foreach($events as $event)
                                    <li class="external-event" style="padding: 5px">
                                        <span id={{$event['id']}} class="text">{{$event['title']}}</span>
                                        <div class="tools">
                                            {{--<i class="fa fa-edit edit-list"></i>--}}
                                            <i class="fa fa-trash-o trash-list"></i>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box-footer clearfix no-border">
                            <button id="add-new-event" type="button" class="btn btn-default pull-right">Create Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('event.create_edit')
    @include('event.create')
    @include('event.edit')
</div>