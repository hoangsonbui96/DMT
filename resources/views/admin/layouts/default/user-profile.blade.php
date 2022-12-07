@extends('admin.layouts.default.app')

<style type="text/css"> 
	/*User Profile*/
	.profileUser .info-user { border: solid 1px #ccc0cc; background: #fff6ff }
	.profileUser .info-user .header {/*background: #28a2a2; */ text-align: center; color:#464646; height: 100%; }
	/*.profileUser .info-user .avatar {height: 150px;width: 150px; background: #446fc0; margin-top: 20px; }*/
	.profileUser .info-user .avatar img { height: 100px; width: auto; margin: 15px auto; }
	.profileUser .info-user .avatar img:hover { cursor: pointer; }
	.profileUser .info-user .change-avatar #avatar-file{ display: none; }
	.profileUser .info-user .fullname{ font-size: 1.3em; text-transform: uppercase; font-weight: bold; margin: 5px auto; }
	.profileUser .info-user .department{ font-style: italic; }
	.profileUser .info-user .below{ text-align: left; height: 100%; padding-bottom: 10px; font-style: italic; text-justify: auto; }

	.profileUser .info-user .avatar {
		position: relative;
		display: inline-block;
		overflow: hidden;
	}

	.profileUser .info-user .avatar #icon-hover-show {
		display: block;
		position: absolute;
		top: -100%;
		opacity: 0;
		left: 0;
		bottom: 0;
		right: 0;
		text-align: center;
		color: inherit;
	}

	.profileUser .info-user .avatar img:hover {
		opacity: 0.5;
	}

	.profileUser .info-user .avatar:hover #icon-hover-show {
		opacity: 1;
		top: 0;
		z-index: 500;
	}

	#icon-hover-show:hover {
		cursor: pointer;
	}

	.profileUser .info-user .avatar:hover #icon-hover-show span {
		top: 50%;
		position: absolute;
		left: 0;
		right: 0;
		transform: translateY(-50%);
	}

	p.input-container {
		width: 100%;
		position: relative;
		top: 10px;
		margin-bottom: 25px;
	}

	p.input-container .bootstrap-select {
		top: 7px;
	}

	p.input-other{
		height: 40px;
		border-bottom: 1px;
	}

	p.skill {
		width: 100%;
		position: relative;
		margin-bottom: 25px;
	}

	label.lbl-input {
		color: #CCC;
		position: absolute;
		cursor: text;
		-webkit-transform: translateY(-25px);
		transform: translateY(-25px);
		-webkit-transition: -webkit-transform 0.3s ease;
		transition: -webkit-transform 0.3s ease;
		transition: transform 0.3s ease;
		transition: transform 0.3s ease, -webkit-transform 0.3s ease;
		left: 0;
		bottom: -25px;
	}

	label.lbl-radio{
		top: 10px !important;
	}

	input.input-text {
		width: 100%;
		height: 40px;
		font-size: 16px;
		-webkit-transition: 0.6s;
		transition: 0.6s;
		border: none;
		border-bottom: 1px solid #CCC;
		background-color: transparent;
		padding-left: 10px;
	}

	input.input-text:focus {
		outline: none;
		border-bottom: 1px solid #28a2a2;
	}

	.animation label.lbl-input {
		-webkit-transform: translateY(-55px);
		transform: translateY(-55px);
		font-size: 10px;
		text-transform: uppercase;
		font-weight: 600;
	}

	.animation-color label {
		color: #28a2a2;
	}

	.social-icon:hover {
		font-weight: bold;
		transition: all 0.5s ease;
	}

	.add-training {
		margin-bottom: 10px;
	}

	.historyTraning .add-training span.btn-add, .remove-training {
		font-size: 1.3em;
		color: #000;
		margin-bottom: 5px;
	}

	.historyTraning .add-training span.btn-add:hover, .remove-training:hover {
		cursor: pointer;
		color: red;
	}
	.tab-content{
		padding: 15px;
	}
	.form-inline{
		margin: 10px 0px;
	}

	.tbl-ProgSkill input.input-text {
		text-align: center;
	}

	.historyTraning h5, .DBLevel h5, .programmingLevel h5, .uploadCV h5 {
		text-transform: uppercase;
		font-weight: bold;
		margin-top: 30px;
	}

	.tbl-skill table tr, .tbl-skill table th, .tbl-skill table td {
		border: 1px solid #ccc !important;
	}
</style>

@section('content')
	<section class="content-header">
		<h1 class="page-header">@lang('admin.user-detail.infoUser')</h1>
	</section>
	<section class="content">
		<form action="" method="POST" id="profile-user-form">
			<div class="profileUser">
				<div class="col-md-3 col-sm-3 col-xs-3 info-user" style="height: fit-content;">

					<div class="header">
						<div class="avatar">
							<img src="{{ isset($user->avatar) ? url($user->avatar) : "/imgs/user-blank.jpg" }}" onerror="this.onerror=null;this.src='/imgs/user-blank.jpg'" name="image-avatar">
							<div id="icon-hover-show"><span class="fa fa-pencil fa-2x"></span></div>
							<div class="input-group hidden">
								<span class="input-group-btn">
								<a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
									<i class="fa fa-picture-o"></i> Choose
								</a>
								</span>
								<input id="thumbnail" class="form-control" type="text" name="avatar" value="{{ isset($user->avatar) ? $user->avatar : "" }}">

							</div>
						</div>
						<div class="fullname">{{$user->FullName}}</div>
					</div>
					<div class="below">
						<div class="department">Nhân viên(<b>
								@if($user->Gender == 0)
									@lang('admin.user.male')
								@else
									@lang('admin.user.female')
								@endif
							</b>),
							<b>{{\Carbon\Carbon::parse($user['Birthday'])->age}}</b> tuổi,
							@if($user->MaritalStt == 0)
								@lang('admin.user.single')
							@else
								@lang('admin.user.married')
							@endif
						</div>
						<div class="curAddr style_real_display">Địa chỉ: {{ isset($user->CurAddress) ? $user->CurAddress : null }}</div>
						<div class="social">
							@if(isset($user->Facebook) || $user->Facebook != null)
								<a class="social-icon" href="{{$user->Facebook}}" target="_blank" style="text-decoration: none;">
									<i class="fa fa-facebook-square" aria-hidden="true"></i> Facebook
								</a>
							@endif
						</div>
					</div>
				</div>
				<div class="col-md-9 col-sm-9 col-xs-9 other-infor">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active">
								<a href="#profile-user" data-toggle="tab">Thông tin cá nhân</a>
							</li>
							<li>
								<a href="#profile-skill" data-toggle="tab">Hồ sơ năng lực</a>
							</li>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="profile-user">
								<div class="row">
									<div class="col-sm-6">
										@if(isset($user->id))
											<input type="text" class="form-control hidden" name="id" value="{{$user->id}}">
										@endif
										<p class="input-container">
											<label class="lbl-input" for="username" unselectable="on">Username:</label>
											<input type="text" id="username" name="username" class="profile-input input-text"
													value="{{isset($user->username) ? $user->username : null}}" disabled>
										</p>
										<p class="input-container">
											<label class="lbl-input" for="fullname" unselectable="on">Họ và tên<sup>*</sup>:</label>
											<input type="text" id="fullname" name="FullName" class="profile-input input-text"
													value="{{isset($user->FullName) ? $user->FullName : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="birthday" unselectable="on">Ngày sinh<sup>*</sup>:</label>
											<input type="text" id="birthday" name="Birthday" class="profile-input input-text"
													value="{{isset($user->Birthday) ? FomatDateDisplay($user->Birthday, FOMAT_DISPLAY_DAY) : null}}">
										</p>
										<p class="input-container input-other animation">
											<label class="lbl-input" for="" unselectable="on">Giới tính:</label>
											<label class="radio-inline lbl-radio">
												<input type="radio" name="Gender" value="0" {{isset($user->Gender) && !$user->Gender ? 'checked' : 'checked'}}>Nam
											</label>
											<label class="radio-inline lbl-radio">
												<input type="radio" name="Gender" value="1" {{isset($user->Gender) && $user->Gender ? 'checked' : ''}}>Nữ
											</label>
										<hr style="border-top: 1px solid #CCC; top: 0px; margin-top: -16px">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="" unselectable="on">Số điện thoại<sup>*</sup>:</label>
											<input type="text" id="tel" name="Tel" class="profile-input input-text"
													value="{{isset($user->Tel) ? $user->Tel : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="email" unselectable="on">Email công ty:</label>
											<input type="email" id="email" name="email" class="profile-input input-text"
													value="{{isset($user->email) ? $user->email :null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="email_user" unselectable="on">Email cá nhân:</label>
											<input type="email" id="email-user" name="email_user" class="profile-input input-text"
												   value="{{isset($user->email_user) ? $user->email_user :null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="perAddr" unselectable="on">Quê quán:</label>
											<input type="text" id="perAddr" name="PerAddress" class="profile-input input-text"
													value="{{isset($user->PerAddress) ? $user->PerAddress : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="curAddr" unselectable="on">Nơi ở hiện tại:</label>
											<input type="text" id="curAddr" name="CurAddress" class="profile-input input-text"
													value="{{isset($user->CurAddress) ? $user->CurAddress : null}}">
										</p>
                                        <p class="input-container">
                                            <label class="lbl-input" for="Instagram" unselectable="on">Instagram:</label>
                                            <input type="text" id="Instagram" name="Instagram" class="profile-input input-text"
                                                   value="{{isset($user->Instagram) ? $user->Instagram : null}}">
                                        </p>
									</div>
                                    @foreach($roomName as $value)
									<div class="col-sm-6">
										<p class="input-container input-other animation">
											<label class="lbl-input" for="RoomName" unselectable="on">Bộ phận:</label>
											<input type="text" id="RoomName" name="RoomId" class="profile-input input-text"
													value="{{isset($value->Name) ? $value->Name : null}}" disabled>
										</p>
                                    @endforeach
										<p class="input-container">
											<label class="lbl-input" for="" unselectable="on">Giờ làm việc:</label>
											<input type="text" id="" name="" class="profile-input input-text"
													value="{{isset($user->STimeOfDay) ? FomatDateDisplay($user->STimeOfDay, 'H:i') : null}}" disabled>
										</p>
											<p class="input-container">
											<label class="lbl-input" for="" unselectable="on">Ngày vào công ty :</label>
											<input type="text" id="sdate" name="SDate" class="profile-input input-text"
													value="{{isset($user->SDate)? FomatDateDisplay($user->SDate, FOMAT_DISPLAY_DAY) : null}}" disabled>
										</p>
										<p class="input-container">
											<label class="lbl-input" for="" unselectable="on">Ngày hết hạn:</label>
											<input type="text" id="expirationdate" name="expirationdate" class="profile-input input-text"
													value="{{isset($user->expirationdate)? FomatDateDisplay($user->expirationdate, FOMAT_DISPLAY_DAY) : null}}" disabled>
										</p>
										<p class="input-container input-other animation">
											<label class="lbl-input" for="" unselectable="on">Tình trạng hôn nhân:</label>
											<label class="radio-inline lbl-radio">
												<input type="radio" name="MaritalStt" value="0" {{isset($user->MaritalStt) && $user->MaritalStt == 0 ? 'checked' : 'checked'}}>Chưa kết hôn
											</label>
											<label class="radio-inline lbl-radio">
												<input type="radio" name="MaritalStt" value="1" {{isset($user->MaritalStt) && $user->MaritalStt == 1 ? 'checked' : ''}}>Đã kết hôn
											</label>
										<hr style="border-top: 1px solid #CCC; top: 0; margin-top: -16px">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="relativeName" unselectable="on">Họ và tên người thân:</label>
											<input type="text" id="relativeName" name="RelativeName" class="profile-input input-text"
													value="{{isset($user->RelativeName) ? $user->RelativeName : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="relationship" unselectable="on">Quan hệ với nhân viên:</label>
											<input type="text" id="relationship" name="Relationship" class="profile-input input-text"
													value="{{isset($user->Relationship) ? $user->Relationship : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="telRelative" unselectable="on">Số điện thoại người thân:</label>
											<input type="text" id="telRelative" name="TelRelative" class="profile-input input-text"
													value="{{isset($user->TelRelative) ? $user->TelRelative : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="facebook" unselectable="on">Facebook:</label>
											<input type="text" id="facebook" name="Facebook" class="profile-input input-text"
													value="{{isset($user->Facebook) ? $user->Facebook : null}}">
										</p>
										<p class="input-container">
											<label class="lbl-input" for="zalo" unselectable="on">Zalo:</label>
											<input type="text" id="zalo" name="Zalo" class="profile-input input-text"
													value="{{isset($user->Zalo) ? $user->Zalo : null}}">
										</p>
									</div>
                                    <div class="col-sm-12">
                                        <p class="input-container">
                                            <label class="lbl-input" for="note" unselectable="on">Ghi chú:</label>
                                            <input type="text" id="note" name="Note" class="profile-input input-text"
                                                   value="{{isset($user->Note) ? $user->Note : null}}">
                                        </p>
                                    </div>
								</div>
							</div>
							<div class="tab-pane" id="profile-skill">
								{{-- <input type="hidden" name="UserID" value="">
								<input type="hidden" name="Action" value="capicityProfile"> --}}
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="levelEN" unselectable="on">Năng lực tiếng Anh</label>
											<input type="text" id="levelEN" class="profile-input input-text" name="LevelEN" value="{{ isset($profile->LevelEN) ? $profile->LevelEN : '' }}">
										</p>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="levelJA" unselectable="on">Năng lực tiếng Nhật</label>
											<input type="text" id="levelJA" class="profile-input input-text" name="LevelJA" value="{{ isset($profile->LevelJA) ? $profile->LevelJA : '' }}">
										</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="yearExperience" unselectable="on">Kinh nghiệm làm việc (năm)</label>
											<input type="text" id="yearExperience" class="profile-input input-text"  name="YearExperience" value="{{ isset($profile->YearExperience) ? $profile->YearExperience : '' }}">
										</p>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="yearInJA" unselectable="on">Số năm làm việc tại Nhật (năm)</label>
											<input type="text" id="yearInJA" class="profile-input input-text" name="YearInJA" value="{{ isset($profile->YearInJA) ? $profile->YearInJA : '' }}">
										</p>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="capacityOther" unselectable="on">Năng lực khác</label>
											<input type="text" id="capacityOther" class="profile-input input-text" name="CapacityOther" value="{{ isset($profile->CapacityOther) ? $profile->CapacityOther : '' }}">
										</p>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12">
										<p class="input-container">
											<label class="lbl-input" for="favorite" unselectable="on">Sở thích</label>
											<input type="text" id="favorite" class="profile-input input-text" name="Favorite" value="{{ isset($profile->Favorite) ? $profile->Favorite : '' }}">
										</p>
									</div>
								</div>
								<p class="input-container">
									<label class="lbl-input" for="note-skill" unselectable="on">Ghi chú</label>
									<input type="text" id="note-skill" class="profile-input input-text" name="NoteProfile" value="{{ isset($profile->Note) ? $profile->Note : '' }}">
								</p>
								{{-- <div class="uploadCV">
									<h5>Upload CV </h5>
									<i class="fa fa-cloud-download" style="font-size:32px;color:red"></i>
									<i class="fa fa-cloud-upload" style="font-size:32px;color:red"></i>
									{{-- <input type="file" id="cv_file" accept="application/pdf">--}}
								{{-- </div> --}}
								<div class="historyTraning">
									<h5>Lịch sử đào tạo</h5>
									<div class="add-training">
										<span class="btn-add" id="add-training">
											<i class="fa fa-plus-circle" aria-hidden="true"></i>
										</span>
									</div>
									@foreach($trainings as $training)
										<div class="form-inline">
											<div class="form-group frm-sDate">
												<div class="input-group date dtpicker">
													<input type="text" class="form-control" placeholder="Năm bắt đầu" name="SYear[]" value="{{ FomatDateDisplay($training->SYear, FOMAT_DISPLAY_DAY) }}">
													<span class="input-group-addon">
														<span class="glyphicon glyphicon-calendar"></span>
													</span>
												</div>
											</div>
											<div class="form-group frm-eDate">
												<div class="input-group date dtpicker">
													<input type="text" class="form-control" placeholder="Năm kết thúc" name="EYear[]" value="{{ FomatDateDisplay($training->EYear, FOMAT_DISPLAY_DAY) }}">
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
												<thead class="thead-default">
													<tr>
														<th class="title name">Ngôn ngữ<br/>Môi trường</th>
														@foreach($chunk as $item)
															<th class="name">{{ $item->Name }}</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="title">Mức độ thành thạo (1 -&gt; 5)</td>
														@foreach($chunk as $item)
															<td class="level animation">
																<input type="text" data-id="1" class="profile-input level-number progLevel-input input-text" name="progSkill[{{ $item->id }}][0]" value="{{ $item->Level > 0 ? $item->Level+0 : '' }}">
															</td>
														@endforeach
													</tr>
													<tr></tr>
													<tr>
														<td class="title">Số năm KN</td>
														@foreach($chunk as $item)
															<td class="year">
																<input type="text" data-id="1" class="profile-input year-number progYear-input input-text" name="progSkill[{{ $item->id }}][1]" value="{{ $item->YearExp>0? $item->YearExp+0 : '' }}">
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
												<thead class="thead-default">
													<tr>
														<th class="title name">Ngôn ngữ<br/>Môi trường</th>
														@foreach($chunk as $item)
															<th class="name">{{ $item->Name }}</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="title">Mức độ thành thạo (1 -&gt; 5)</td>
														@foreach($chunk as $item)
															<td class="level animation">
																<input type="text" data-id="1" class="profile-input level-number DBLevel-input input-text" name="dbSkill[{{ $item->id }}][0]" value="{{ $item->Level > 0 ? $item->Level+0 : '' }}">
															</td>
														@endforeach
													</tr>
													<tr></tr>
													<tr>
														<td class="title">Số năm KN</td>
														@foreach($chunk as $item)
															<td class="year">
																<input type="text" data-id="1" class="profile-input year-number DBYear-input input-text" name="dbSkill[{{ $item->id }}][1]" value="{{ $item->YearExp>0? $item->YearExp+0 : '' }}">
															</td>
														@endforeach
													</tr>
													<tr></tr>
												</tbody>
											</table>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<div class="input-container text-right" style="margin-right:15px">
			<button class="btn btn-primary" id="save_profile">@lang('admin.btnSave')</button>
		</div>
		<div class="Temp-training" id="temp-training" style="display: none;">
			<div class="form-inline">
                <div class="input-group date dtpicker">
                    <input type="text" class="form-control" placeholder="Năm bắt đầu" name="SYear[]">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                <div class="input-group date dtpicker">
                    <input type="text" class="form-control" placeholder="Năm kết thúc" name="EYear[]">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
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
	</section>
@endsection

@section('js')
	<script type="text/javascript" async>
		$(document).ready(function() {
			$('#sdate, #birthday,#eDate, #officalDate, #DateCreateCMND').datetimepicker({
				format: 'DD/MM/YYYY',
			});
			SetDatePicker($('#sdate, #birthday,#eDate, #officalDate, #DateCreateCMND'))
			$('#add-training').click(function () {
                SetDatePicker($('.dtpicker'));
            })
			$('#sTimeOfDay,#eTimeOfDay').datetimepicker({
				allowInputToggle: true,
				format: 'HH:mm',
				stepping: 15
			});

			$(".profile-input").each(function() {
				if ($(this).val() != "") {
					$(this).parent().addClass("animation");
				}
			});

			// Add animation when input is focused
			$(".profile-input").focus(function() {
				$(this).parent().addClass("animation animation-color");
			});

			// Remove animation(s) when input is no longer focused
			$(".profile-input").focusout(function() {
				if ($(this).val() === "") {
					$(this).parent().removeClass("animation");
				}
				$(this).parent().removeClass("animation-color");
			});

			$('.input-container').click(function() {
				$(this).children('input').focus();
			});

			$('.profileUser input#tel').keyup(function() {
				var str = $(this).val();
				str = str.replace(/\D/g, "");
				$(this).val(str);
			});
		});
		$('#save_profile').click(function() {
		    console.log($('#profile-user-form').serializeArray());
			ajaxGetServerWithLoader('{{ route('admin.storeProfile') }}', 'POST', $('#profile-user-form').serializeArray(), function (data) {
				// console.log(data);
				if (typeof data.errors !== 'undefined') {
					showErrors(data.errors);
					return;
				}
    return;
				locationPage();
			});
		});

		$('#icon-hover-show').on('click', function() {
			$('#lfm').click();
		});


        $('#lfm').filemanager('image', {prefix: route_prefix});
		$("input[name=avatar]").change(function(){
			$('img[name=image-avatar]').attr('src', $(this).val());
			$('#profile-user-form').append('<input type="hidden" name="blnAva" value="1">');
			$('#save_profile').click();
		});
	</script>

@endsection
