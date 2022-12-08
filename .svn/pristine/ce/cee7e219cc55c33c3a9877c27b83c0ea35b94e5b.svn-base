@extends('admin.layouts.default.app')
@push('pageCss')
{{--    <style src="{{ asset('css/capaProfile.css') }}"></style>--}}
    <link rel="stylesheet prefetch" href="{{ asset('css/capaProfile.css') }}">
@endpush
@section('content')
    <div id="container">
        <div id="popupModal">

        </div>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.skill.profile') - {{ $user->FullName }}</h1>

            </div>
        </div>

        <div class="table-data">

            <div class="profileUser">
                <div class="save-errors"></div>
                <form id="capicity-profile">
                    <div class="row">
                        <div class="col-md-3 col-sm-12 col-xs-12 info-user">
                            <div class="header">
                                <div class="avatar">
                                    {{-- <img src="../Upload/Avatar/Male.png" alt="thang" onclick="eventClickAvatar()"> --}}
                                </div>
                                <div class="change-avatar">
                                    <input id="avatar-file" type="file" class="file" multiple="" data-show-upload="false" class="form-control" data-show-caption="true" accept="image/x-png,image/gif,image/jpeg">
                                </div>
                                <div class="fullname">{{Auth::user()->FullName}}</div>
                            </div>
                            <div class="below">
                                <div class="department"> (<strong>
                                    @if(Auth::user()->Gender==1){
                                        @lang('admin.user.male') ,
                                    @else

                                        @lang('admin.user.female'),
                                    @endif
                                </strong>)19 tuổi ,
                                    @if(Auth::user()->MaritalStt==1){
                                        @lang('admin.user.married') ,
                                    @else
                                        @lang('admin.user.single'),
                                    @endif
                                </div>
                                <div class="curAddr">Địa chỉ: {{Auth::user()->CurAddress}}</div>
                            </div>
                        </div>

                        <div class="col-md-9 col-sm-12 col-xs-12 other-infor">
                            <input type="hidden" name="UserID" value="{{ $user->id }}">
                            <input type="hidden" name="Action" value="capicityProfile">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="levelEN" class="profile-input" name="LevelEN" value="{{ isset($profile->LevelEN) ? $profile->LevelEN : '' }}">
                                        <label for="" unselectable="on">Năng lực tiếng Anh</label>
                                    </p>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="levelJA" class="profile-input"  name="LevelJA" value="{{ isset($profile->LevelJA) ? $profile->LevelJA : '' }}">
                                        <label for="" unselectable="on">Năng lực tiếng Nhật</label>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="yearExperience" class="profile-input"  name="YearExperience" value="{{ isset($profile->YearExperience) ? $profile->YearExperience : '' }}">
                                        <label for="input-username" unselectable="on">Kinh nghiệm làm việc (năm)</label>
                                    </p>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="yearInJA" class="profile-input" name="YearInJA" value="{{ isset($profile->YearInJA) ? $profile->YearInJA : '' }}">
                                        <label for="" unselectable="on">Số năm làm việc tại Nhật (năm)</label>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="capacityOther" class="profile-input" name="CapacityOther" value="{{ isset($profile->CapacityOther) ? $profile->CapacityOther : '' }}">
                                        <label for="" unselectable="on">Năng lực khác</label>
                                    </p>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <p class="input-container animation">
                                        <input type="text" id="favorite" class="profile-input" name="Favorite" value="{{ isset($profile->Favorite) ? $profile->Favorite : '' }}">
                                        <label for="" unselectable="on">Sở thích</label>
                                    </p>
                                </div>
                            </div>
                            <p class="input-container animation">
                                <input type="text" id="note" class="profile-input" name="Note" value="{{ isset($profile->Note) ? $profile->Note : '' }}">
                                <label for="" unselectable="on">Ghi chú</label>
                            </p>
                        </div>
                    </div>
                    <div class="uploadCV">
                        <h5>Upload CV </h5>
                        <i class="fa fa-cloud-download" style="font-size:48px;color:red"></i>
                        <i class="fa fa-cloud-upload" style="font-size:48px;color:red"></i>
                        {{-- <input type="file" id="cv_file" accept="application/pdf"> --}}
                    </div>
                    <div class="historyTraning">
                        <h5>Lịch sử đào tạo</h5>
                        <div class="add-training"><span class="btn-add" id="add-training"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                        </div>

                        @foreach($trainings as $training)
                            <div class="form-inline">
                                <div class="form-group frm-sDate">
                                    <div class="input-group date sDate">
                                        <input type="text" class="form-control sDate-input" id="" placeholder="Năm bắt đầu" name="SYear[]" value="{{ $training->SYear }}">
                                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                    </div>
                                </div>
                                <div class="form-group frm-eDate">
                                    <div class="input-group date eDate">
                                        <input type="text" class="form-control eDate-input" placeholder="Năm kết thúc" name="EYear[]" value="{{ $training->EYear }}">
                                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                    </div>
                                </div>
                                <div class="form-group frm-content">
                                    <input type="text" id="historyTraning" class="form-control content-training" placeholder="Nội dung đào tạo" name="Content[]" value="{{ $training->Content }}">
                                </div>
                                <div class="form-group frm-icon-remove">
                                    <div class="remove-training"><i class="fa fa-times" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="programmingLevel">
                        @foreach($progSkills->chunk(8) as $chunk)
                            <h5>Ngôn ngữ môi trường</h5>
                            <div class="table-responsive tbl-skill tbl-ProgSkill">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="title name">Ngôn ngữ/Môi trường</th>
                                            @foreach($chunk as $item)
                                                <th class="name">{{ $item->Name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="title">Mức độ thành thạo
                                                <br>Từ 1-&gt;5</td>
                                            @foreach($chunk as $item)
                                                <td class="level animation">
                                                    <input type="text" data-id="1" class="profile-input level-number progLevel-input" name="progSkill[{{ $item->id }}][0]" value="{{ $item->Level > 0 ? $item->Level+0 : '' }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr></tr>
                                        <tr>
                                            <td class="title">Số năm KN</td>
                                            @foreach($chunk as $item)
                                                <td class="year">
                                                    <input type="text" data-id="1" class="profile-input year-number progYear-input" name="progSkill[{{ $item->id }}][1]" value="{{ $item->YearExp>0? $item->YearExp+0 : '' }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr></tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    <div class="programmingLevel">
                        @foreach($dbSkills->chunk(8) as $chunk)
                            <h5>Database</h5>
                            <div class="table-responsive tbl-skill tbl-ProgSkill">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="title name">Ngôn ngữ/Môi trường</th>
                                            @foreach($chunk as $item)
                                                <th class="name">{{ $item->Name }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="title">Mức độ thành thạo
                                                <br>Từ 1-&gt;5</td>
                                            @foreach($chunk as $item)
                                                <td class="level animation">
                                                    <input type="text" data-id="1" class="profile-input level-number DBLevel-input" name="dbSkill[{{ $item->id }}][0]" value="{{ $item->Level > 0 ? $item->Level+0 : '' }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr></tr>
                                        <tr>
                                            <td class="title">Số năm KN</td>
                                            @foreach($chunk as $item)
                                                <td class="year">
                                                    <input type="text" data-id="1" class="profile-input year-number DBYear-input" name="dbSkill[{{ $item->id }}][1]" value="{{ $item->YearExp>0? $item->YearExp+0 : '' }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr></tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </form>

                <div class="btn-save">
                    {{-- <button type="button" class="btn btn-default" id="cancel">Cancel</button>
                    <button type="button" class="btn btn-danger" id="delete">Delete</button> --}}
                    <button type="button" class="btn btn-primary" id="saveProfile">Save</button>
                </div>

                <div class="error"></div>
            </div>
        </div>
    </div>
    <div class="Temp-training" id="temp-training" style="display: none;">
        <div class="form-inline">
            <div class="form-group frm-sDate">
                <div class="input-group date sDate">
                    <input type="text" class="form-control sDate-input" id="" placeholder="Năm bắt đầu" name="SYear[]">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group frm-eDate">
                <div class="input-group date eDate">
                    <input type="text" class="form-control eDate-input" placeholder="Năm kết thúc" name="EYear[]">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group frm-content">
                <input type="text" id="historyTraning" class="form-control content-training" placeholder="Nội dung đào tạo" name="Content[]">
            </div>
            <div class="form-group frm-icon-remove">
                <div class="remove-training"><i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

<script>
    var profileUrl = "{{ route('admin.ProfileSkill') }}";
</script>

@endsection
