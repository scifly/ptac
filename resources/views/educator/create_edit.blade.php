<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($educator) && !empty($educator['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $educator['id']]) }}
            @endif

            @include('partials.single_select', [
                'label' => '教职员工',
                'id' => 'user_id',
                'items' => $users
            ])

            @include('partials.multiple_select', [
                'label' => '所属组',
                'for' => 'team_ids',
                'items' => $teams,
                'selectedItems' => isset($selectedTeams) ? $selectedTeams:[]
            ])

            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])

            <div class="form-group">
                {!! Form::label('sms_quote', '可用短信条数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('sms_quote', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            @include('partials.enabled', ['enabled' => isset($educator['enabled']) ? $educator['enabled'] : ""])

        </div>
    </div>
    @include('partials.form_buttons')
</div>
