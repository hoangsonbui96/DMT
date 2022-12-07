<form class="form-inline" id="meeting-search-form" action="" method="">
    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
            @if(Auth::user()->role_group == 3)
                <option value="{{Auth::user()->id}}">{{Auth::user()->FullName}}</option>
            @endif
            @can('admin', $menu)
            <option value="">@lang('admin.chooseUser')</option>
            {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : '')!!}
            @endcan
        </select>
    </div>
    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-ProjectID" name="ProjectID" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
            <option value="">@lang('admin.overtime.project')</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"  {{ isset($request['ProjectID'] ) && $request['ProjectID'] == $project->id ? 'selected' : '' }}>{{ $project->NameVi }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control dtpicker" placeholder="@lang('admin.startDate')" autocomplete="off" id="s-date" name="date[]"
                   value="{{ isset($request['date'] ) ? $request['date'][0] : GetStartMoth() }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control dtpicker" placeholder="@lang('admin.endDate')" autocomplete="off" id="e-date" name="date[]"
                   value="{{ isset($request['date'] ) ? $request['date'][1] : GetEndMoth() }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-search" name="seach" id="btn-search-meeting">@lang('admin.btnSearch')</button>
        @can('action',$export)
            <button class="btn btn-success" id="btn-export">@lang('admin.export-excel')</button>
        @endcan
    </div>
</form>
<script type="text/javascript" async>
    $(".selectpicker").selectpicker({
        noneSelectedText : ''
    });
    $(function () {
        SetDatePicker($('.date'));
        var sTime = $('#s-date').val();
        var eTime = $('#e-date').val();
        var user  = $('#select-user option:selected').val();
        var project  = $('#select-ProjectID option:selected').val();

        //js click export excel
        $('#btn-export').click(function (e) {
            e.preventDefault();
            var reqSearch = window.location.search;
            ajaxGetServerWithLoader('{{ route('export.listOvertime') }}'+reqSearch, 'GET', null, function (data) {
                if (typeof data.errors !== 'undefined'){
                    showErrors(data.errors);
                    return;
                }
                window.location.href = '{{ route('export.listOvertime') }}'+reqSearch;
            });
        });
    });
</script>
