{{--<div class="three-layer">--}}
{{--    <label class="control-label col-md-3" for="" style="text-align: left">{{ $label }}&nbsp;<sup--}}
{{--            class="text-red">*</sup>:</label>--}}
{{--    <div class="col-md-9 select-abreason">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-5">--}}
{{--                <select class="selectpicker show-tick show-menu-arrow {{ $class1 ?? '' }}"--}}
{{--                        data-actions-box="{{ $data_action_box_1 ?? true }}"--}}
{{--                        id="{{$data_id1 ?? ''}}"--}}
{{--                        data-size="{{$data_size1 ?? 5}}"--}}
{{--                        name="{{$data_name1 ?? ''}}"--}}
{{--                        data-live-search="{{$data_live_search1 ?? true}}"--}}
{{--                        data-live-search-placeholder="{{$data_live_search_placeholder1 ?? 'Search'}}"--}}
{{--                        {{$style_choose1 ?? 'multiple'}}--}}
{{--                        title="{{$title1 ?? ''}}">--}}
{{--                    {{ $genHtmlOption1 }}--}}
{{--                </select>--}}
{{--                <select class="selectpicker show-tick show-menu-arrow {{ $class2 ?? '' }}"--}}
{{--                        data-actions-box="{{ $data_action_box_2 ?? true }}"--}}
{{--                        id="{{$data_id2 ?? ''}}"--}}
{{--                        data-size="{{$data_size2 ?? 5}}"--}}
{{--                        name="{{$data_name2 ?? ''}}"--}}
{{--                        data-live-search="{{$data_live_search2 ?? true}}"--}}
{{--                        data-live-search-placeholder="{{$data_live_search_placeholder2 ?? 'Search'}}"--}}
{{--                        {{$style_choose2 ?? 'multiple'}}--}}
{{--                        title="{{$title2 ?? ''}}">--}}
{{--                    {{ $genHtmlOption2 }}--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="col-md-7">--}}
{{--                <table class="table table-striped" id="three-layer">--}}
{{--                    <thead>--}}
{{--                    <tr>--}}
{{--                         {{ $columnsTable ?? '' }}--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody style="{{$tbody_style}}">--}}
{{--                        {{ $dataTable ?? '' }}--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<div class="three-layer">
    <div class="form-group">
        <label class="control-label col-sm-3" for="">{{ $label }}&nbsp;<sup
                class="text-red">*</sup>:</label>
        <div class="col-sm-9 select-abreason">
            <div class="form-row" style="display: flex; justify-content: space-between">
                    <select class="selectpicker show-tick show-menu-arrow {{ $class1 ?? '' }}"
                            data-actions-box="{{ $data_action_box_1 ?? true }}"
                            id="{{$data_id1 ?? 'data_id1'}}"
                            data-size="{{$data_size1 ?? 5}}"
                            name="{{$data_name1 ?? ''}}"
                            data-live-search="{{$data_live_search1 ?? true}}"
                            data-live-search-placeholder="{{$data_live_search_placeholder1 ?? 'Search'}}"
                            {{$style_choose1 ?? 'multiple'}}
                            title="{{$title1 ?? ''}}"
                            data-width="30%">
                        {{ $genHtmlOption1 }}
                    </select>
                    <select class="selectpicker show-tick show-menu-arrow {{ $class2 ?? '' }}"
                            data-actions-box="{{ $data_action_box_2 ?? true }}"
                            id="{{$data_id2 ?? 'data_id2'}}"
                            data-size="{{$data_size2 ?? 5}}"
                            name="{{$data_name2 ?? ''}}"
                            data-live-search="{{$data_live_search2 ?? true}}"
                            data-live-search-placeholder="{{$data_live_search_placeholder2 ?? 'Search'}}"
                            {{$style_choose2 ?? 'multiple'}}
                            title="{{$title2 ?? ''}}"
                            data-width="68%">
                        {{ $genHtmlOption2 }}
                    </select>
            </div>
        </div>
    </div>
    <div class="form-group" id="memberGroup">
        <label for="" class="control-label col-sm-3">Người tham gia&nbsp;<span id="countMember"></span>:</label>
        <div class="col-sm-9">
            <table class="table table-striped" id="three-layer">
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    {{ $columnsTable ?? '' }}--}}
{{--                </tr>--}}

                </thead>
                <tbody style="{{$tbody_style}}">
                {{ $dataTable ?? '' }}
                </tbody>
            </table>
        </div>
    </div>
</div>
