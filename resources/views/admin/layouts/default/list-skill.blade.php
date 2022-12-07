@extends('admin.layouts.default.app')

@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.skill.management')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <form class="form-inline" id="form-search" action="" method="">
{{--                        <div class="form-group pull-left margin-r-5">--}}
{{--                            <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">--}}
{{--                        </div>--}}
                        <div class="form-group pull-left">
                            <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="user" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
                                <option value="">@lang('admin.staff')</option>
                                {!! GenHtmlOption($users, 'id', 'FullName', isset($request['user']) ? $request['user'] : '') !!}
                            </select>
                        </div>
                        <div class="form-group pull-left">
                            <button type="submit" class="btn btn-primary btn-search" id="btn-search" >@lang('admin.search')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th>@lang('admin.user.full_name')</th>
                        <th class = "width8">@lang('admin.user.age')</th>
                        <th class = "width8">@lang('admin.user.year_exp')</th>
                        <th>@lang('admin.user.prog_skill') (Level - YearExp)</th>
                        <th>@lang('admin.user.db_skill') (Level - YearExp)</th>
                        <th class="width8">@lang('admin.action')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                        @foreach($list as $key => $item)
                            <tr>
                                <td class = "center-important">{{ $key + 1 }}</td>
                                <td class = "left-important">{{ $item->FullName }}</td>
                                <td >{{ \Carbon\Carbon::now()->diffInYears($item->BirthDay) }}</td>
                                <td>{{ $item->YearExperience }}</td>
                                <td class = "left-important">
                                    @foreach($item->progSkills as $progSkill)
                                        {{ $progSkill->Name }} : {{ $progSkill->Level+0 }} - {{ $progSkill->YearExp+0 }} <br>
                                    @endforeach
                                </td>
                                <td class = "left-important">
                                    @foreach($item->DbSkills as $dbSkill)
                                        {{ $dbSkill->Name }} : {{ $dbSkill->Level+0 }} - {{ $dbSkill->YearExp+0 }} <br>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <span class="update edit update-skill-one" item-id="{{ $item->UserID }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                </td>
                            </tr>
                        @endforeach
                @endslot
                {{-- @slot('pageTable')
                @endslot --}}
            @endcomponent
            <div id="popupModal">
            </div>
        </div>
    </div>
</section>
<script>
    $('.update-skill-one').click(function () {
        var id = $(this).attr('item-id');
        window.location.href = '{{ route('admin.ProfileUser') }}/'+id;
    });
</script>
@endsection
