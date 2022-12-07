@extends('admin.layouts.default.app')
@push('pageJs')
<script type="text/javascript" src="{{ asset('js/amlich-hnd.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/coremain.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/daygridmain.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/momentmain.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/interactionmain.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/timegridmain.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('js/listmain.js') }}" defer></script>
{{-- <script type="text/javascript" src="{{ asset('themes/adminlte/bootstrap/dist/js/bootstrap.min.js') }}" async></script> --}}
{{-- <script type="text/javascript" src="{{ asset('themes/adminlte/bootstrap/dist/js/bootstrap.min.js') }}" defer></script> --}}
@endpush
@push('pageCss')
<link rel="stylesheet" href="{{ asset('css/Layout.css') }}">
<link rel="stylesheet" href="{{ asset('css/coremain.css') }}">
<link rel="stylesheet" href="{{ asset('css/daygridmain.css') }}">
<link rel="stylesheet" href="{{ asset('css/timegridmain.css') }}">
<link rel="stylesheet" href="{{ asset('css/listmain.css') }}">
@endpush
<style>
    .notempty .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 3;
        top: 115%;
        left: 50%;
        margin-left: -60px;
    }

    .notempty .tooltiptext::after {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent black transparent;
    }

    .notempty:hover .tooltiptext {
        visibility: visible;
    }
</style>
@section('content')
<div id="container">
	{{-- <div class="collapse navbar-collapse" id="myNavbar">
		<ul class="nav navbar-nav hidden">
				<li>
				<label style="padding-top: 17px;padding-right: 7px;float: left;">Chọn kiểu: </label>
				<div class="type-print" style="padding-top: 7px;padding-right: 7px;float: left;">
					<div style="float: left;">
					<select class='selectpicker show-tick show-menu-arrow' id='type-print-date'>";
						<option value='0' selected >Ngày Việt - D/M/Y </option>
						<option value='1'>Ngày Nhật - Y/M/D</option>
					</select>
					</div>
				</div>
				@can('action',$export)
				<button type="button" class="btn btn-primary hidden" id="print" style="margin-top: 7px;">Xuất PDF</button>
				@endcan
			</li>
		</ul>
	</div> --}}
	<div class="container-fluid" id="containers" >
		<div class="header">
			<div class="date-now">
				<span></span>
			</div>
			<div class="title">
				<h1 id="title"></h1>
				<H2 id="year-luner"></H2>
			</div>
			<div class="year" style="margin-bottom: 10px;">
				<label style="padding-top: 7px;padding-right: 7px;">@lang('admin.calendar.selectCalendar')</label>
				<div class="select-calendar">
					<form method="get" id="filterSearch">
						<select class='selectpicker show-tick show-menu-arrow' id='select-calendar' name = "select-calendar">";
							@foreach($calendars as $calendar)
								<option value="{{ $calendar->id }}" data-title = "{{$calendar->Title}}" {{ isset($request['CalendarID'] ) && $request['CalendarID'] == $calendar->id ? 'selected' : '' }} >{{ $calendar->Name }}</option>
							@endforeach
						</select>

						<select class='selectpicker show-tick show-menu-arrow selectyear' id='select-year' name ="select-year">";
							@for ($i = $years - 10; $i <= $years + 10; $i++) {
								@if ($i == $years) {
									<option value="{{$i}}" selected>{{$i}}</option>
								}@else {<option value="{{$i}}">{{$i}}</option>}
								@endif
							}
							@endfor
						</select>
						<!-- Tien 22/4/2020 -->
						<select class='selectpicker show-tick show-menu-arrow' name ='type-print' id='type-print'>";
							<option value='0' selected >@lang('admin.calendar.years')</option>
							<option value='1'>@lang('admin.calendar.month')</option>
							<option value='2'>@lang('admin.calendar.week')</option>
						</select>
						<!-- Tien 22/4/2020 -->
						<select class='selectpicker show-tick show-menu-arrow' id='type-print-date'  name ='type-print-date'>";
							<option value='0' selected >@lang('admin.calendar.dayVietNam')</option>
							<option value='1'>@lang('admin.calendar.dayJapan')</option>
						</select>
					</form>
				</div>
				<div class="select-year"></div>
			</div>
			<div class="btn-expand" style="margin-bottom: 10px;">
				@can('action',$copyC)
				<button type="button" class="btn btn-primary" item-id="<?php echo $_SESSION['year']; ?>" id="copyCalendar" item-id1="<?php echo $_SESSION['CalendarID']; ?>">@lang('admin.calendar.copyCalendar')</button>
				@endcan

                @can('action',$deleteC)
				<button type="button" class="btn btn-danger" id="delCalendar" item-id="<?php echo $_SESSION['year']; ?>" id="copyCalendar" item-id1="<?php echo $_SESSION['CalendarID']; ?>">@lang('admin.calendar.deleCalendar')</button>
				@endcan

				@can('action',$export)
				<button type="button" class="btn btn-primary" id="print">@lang('admin.Export_PDF')</button>
				@endcan
			</div>
		</div>
		<div class="hr" style="height: 2px;width: 80%;background: #d8dfe5;margin-bottom: 20px;margin-left: 10%;"></div>
		<div class="content" id="content">
			<div class="year-title" id="year-title">
				<div class="row">
					<div class="col-md-4 col-sm-4 col-sx-12">
						<span id="year"></span>
					</div>
				</div>
			</div>

			<!-- List month of year -->
			<div class="row">
				<div class="list-year" id="list-year">

				</div>
			</div>
		</div>

		<div class="print-detail-data"></div>
		<div id="month-of-year"></div>
	</div>
	<div id="popupModal">
        {{-- @include('admin.includes.user-detail')--}}
	</div>
</div>
@endsection
@section('js')
<script type="text/javascript" async>
	var ajaxUrl = "{{ route('admin.CalendarItem') }}";
    var newTitle = 'Thêm sự kiện';
    var updateTitle = 'Cập nhật sự kiện';
    var ajaxUrlCopy = "{{ route('admin.CalendarYear') }}";
    var copyTitle = 'Sao chép sự kiện';
    $('body').addClass('sidebar-collapse');
	$(document).ready(function() {
		$('.selectyear .dropdown-menu').css({
			"height": "156px",
		});
		$('#side-menu').hide();
		$('#page-wrapper').css({
			margin:"0px",
			padding:"0px",
		});
		var calendar = $("#select-calendar option:selected").text();
		if(!!!calendar) {
			$('#print').prop( "disabled", true );
			$('#copyCalendar').prop( "disabled", true );
			$('#delCalendar').prop( "disabled", true );
			alert('Hiện tại chưa có lịch, vui lòng vào tạo lịch!Nhấn vào ok để tiến hành tạo lịch!');
			window.location.href = "CalendarManagement";
		} else {
			loadCalendar();
		}

		$('#type-print').change(function() {
			var ajaxUrl = "{{ route('admin.Calendar') }}";
			$('#filterSearch').submit();
			loadCalendar();
		});

		$('#select-year').change(function() {
			var ajaxUrl = "{{ route('admin.Calendar') }}";
			$('#filterSearch').submit();
			loadCalendar();
		});

		$('#select-calendar').change(function() {
			var ajaxUrl = "{{ route('admin.Calendar') }}";
			$('#filterSearch').submit();
			loadCalendar();
		});

		$('#type-print-date').change(function() {
			var ajaxUrl = "{{ route('admin.Calendar') }}";
			$('#filterSearch').submit();
			loadCalendar();
			setTitleCalendar();
		});

		$("#print").click(function() {
			var type = $('#type-print').val();
			var date_type = $('#type-print-date').val();
			$('.year,.btn-expand').hide();
			$('#myNavbar').remove();
			if (type == 0) {
				printAllData(date_type,type);
				window.print();
				$('.loadajax').show();
				window.location.reload();
				$('.print-detail-data').empty();
			} else if (type == 1) {
				$('.header h1, .header h2').hide()
				$('#list-year').html('');
				loadByMonth(date_type);
				$('#container').css({
					width: '1500px',
				});
				$('span.birthday').css({
					display: 'block',
				});
				window.print();
				$('.loadajax').show();

				$('#month-of-year').html('');
				window.location.reload();
			} else {
				printAllData(date_type,type);
				window.print();
				$('.loadajax').show();
				window.location.reload();
				$('.print-detail-data').empty();
			}
		});
	});

	function loadCalendar() {
		if('<?php echo $_SESSION['calendarchanger']; ?>' != 2) {
			$('button#print').removeClass('hidden');
		}
		<?php if(isset($_SESSION['CalendarID'])) { ?>
			$('#select-calendar').selectpicker('val', '<?php echo $_SESSION['CalendarID']; ?>');
		<?php } else { ?>
			$('#select-calendar').selectpicker('val', $id);
		<?php } ?>

		<?php if(isset($_SESSION['year'])) { ?>
			$('#select-year').selectpicker('val', '<?php echo $_SESSION['year']; ?>');
		<?php } ?>

		<?php if(isset($_SESSION['calendarchanger'])) { ?>
			$('#type-print').selectpicker('val', '<?php echo $_SESSION['calendarchanger']; ?>');
		<?php } ?>

		<?php if(isset($_SESSION['calendarchanger'])) { ?>
			$('#type-print-date').selectpicker('val', '<?php echo $_SESSION['calendartype']; ?>');
		<?php } ?>

		var calendar = $("#select-calendar option:selected").text();
		$('#myModal .modal-header h4.modal-title').html('');
		$("#myModal .modal-header h4.modal-title").append(calendar);
		$(".header h1#title").empty();
		$(".header h2#year-luner").empty();
		var strYear = $("#select-year").val().trim();
		var intyear = parseInt(strYear);
		var canchi = getYearCanChi(intyear);
		setTitleCalendar();
		$(".header h2#year-luner").append("("+canchi+")");

		//22/4/2020
		var calendarchanger = '<?php echo $_SESSION['calendarchanger']; ?>';
		if(calendarchanger != '2' && calendarchanger != '1') {
			//end 22/4/2020
			setOutputSize("large");
			var s = printYear(intyear);
			var d = window.location.href;
			var r = d.replace('admin/Calendar','js/amlich-hnd.js');
			var t= s.replace('amlich-hnd.js',r);
			$('#list-year').html('');
			$("#list-year").append(t);
			var calendarID = getUrlVars()["calendarID"];
			if (calendarID && COUNT_LOAD_CALENDAR < 1) {
				idCalendar = calendarID;
				$('#select-calendar').val(idCalendar).selectpicker('refresh');
			} else {
				idCalendar = $('#select-calendar option:selected').val().trim();
			}
			changeColor(idCalendar, intyear);
			//22/4/2020
		} else {
			weekCalendar(intyear);
		}
		//end 22/4/2020
	}

	function convertDateType(date_type,strDate) {
		var date = '';
		if (strDate != '') {
			if (date_type == 0) {
				if(!!!strDate) {

				} else {
					date = strDate.substring(8,10)+ "/" + strDate.substring(5,7) + "/" + strDate.substring(0,4);
				}
			} else {
				if(!!!strDate) {

				} else {
					date = strDate.substring(0,4)+ "年" + strDate.substring(5,7) + "月" + strDate.substring(8,10)+"日";
				}
			}
		}
		return date;
	}

	function setTitleCalendar() {
		var date_type = $('#type-print-date').val();
		var strTitle = $( "#select-calendar option:selected" ).attr('data-title');
		var strYear = $("#select-year").val().trim();
		if (date_type == 0) {
			$('.header h1#title').html('');
			$('.header h1#title').append(strTitle+ '&nbsp;' + strYear)
		} else {
			$('.header h1#title').html('');
			$('.header h1#title').append(strYear+'年&nbsp;&nbsp;&nbsp;'+strTitle)
		}
	}

	function getUrlVars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	function loadByMonth(date_type) {
		var strYear = $("#select-year").val().trim();
		var intyear = parseInt(strYear);
		var idCalendar = $('#select-calendar option:selected').val().trim();
		setOutputSize("large");
		var s = printMonthOfYear(date_type,intyear);
		$('#month-of-year').html('');
		$("#month-of-year").append(s);
		changeColor(idCalendar,intyear);
	}

    function changeColor(idCalendar,intyear) {
		var arrEvent = new Array();
		var user = new Array();
		arrEvent = <?php echo $calendars_events ?>;
		DATA_EVENT = arrEvent;
		arrUser = <?php echo $users ?>;

		if (arrEvent != "" || arrUser !="") {
			// Fild and addClass for date
			$(".thang tbody tr td.notempty").each(function() {
				// Get date from attribute
				var dd = $(this).attr("data-day");
				var mm = $(this).attr("data-month");
				var yyyy = $(this).attr("data-year");
				var date = yyyy + "" + mm + "" + dd;
				var intdate = parseInt(date);
				var day = mm + "" + dd;

				for (var i = 0; i < arrEvent.length; i++) {
					if (intdate >= parseInt(arrEvent[i].StartDate.substring(0,4)+arrEvent[i].StartDate.substring(5,7)+arrEvent[i].StartDate.substring(8,10)) && intdate <= parseInt(arrEvent[i].EndDate.substring(0,4)+arrEvent[i].EndDate.substring(5,7)+arrEvent[i].EndDate.substring(8,10))) {
						var content = arrEvent[i].Content;
						//Add data-id
						$(this).attr("item-id", arrEvent[i].id);
						$(this).attr("data-toggle", "tooltip");
						$(this).attr("data-placement", "top");
						$(this).attr("data-container", "body");
						$(this).attr("title", content);
                        $( this ).append( $('<span class="tooltiptext">'+content+'</span>' ) );
						if(arrEvent[i].Type == 1) {
							$(this).addClass("nghi");
						}
						if(arrEvent[i].Type == 0) {
							$(this).addClass("lambu");
						}
						if(arrEvent[i].Type == 2) {
							var jsColor = $('#myModal #jaColor input#hexColor').val();
							$(this).addClass("nghikhac");
							if (arrEvent[i].jaColor != '') {
								$(this).attr("style", 'background: ' + arrEvent[i].jaColor + ' !important');
							} else {
								$(this).attr("style", 'background: ' + jaColor + ' !important');
							}
						}
						if(arrEvent[i].Type == 3) {
							$(this).addClass("nghiKBS");
						}
						break;
					}
				}

				for (var i = 0; i < arrUser.length; i++) {
					if(!!!arrUser[i].Birthday) {
						continue;
					} else {
						var birthday1 = arrUser[i].Birthday.substring(5,7);
						var birthday2 = arrUser[i].Birthday.substring(8,10);
						var birthday = birthday1+birthday2;

						if (day == birthday) {
							$(this).append('<span class="birthday"><i class="fa fa-gift" aria-hidden="true"></i></span>');
							break;
						}
					}
				}

			});
		}
	}

	function setTitleCalendar() {
		var date_type = $('#type-print-date').val();
		var strTitle = $( "#select-calendar option:selected" ).attr('data-title');
		var strYear = $("#select-year").val().trim();
		if (date_type == 0) {
			$('.header h1#title').html('');
			$('.header h1#title').append(strTitle+ '&nbsp;' + strYear)
		} else {
			$('.header h1#title').html('');
			$('.header h1#title').append(strYear+'年&nbsp;&nbsp;&nbsp;'+strTitle)
		}
	}

	function printAllData(date_type,type) {
		var idCalendar = $('#select-calendar option:selected').val().trim();
		var intyear = parseInt($("#select-year").val().trim());
		var arrEvent = new Array();
		var html = '';

		if(type != 2) {
			arrEvent = DATA_EVENT;
			var count = arrEvent.length;
			if (count > 0) {
				head = '', h1 = '', h2 = '', footer = '';
				head += '<table class="table table-bordered" id="infoyear"><thead><tr><th>Date</th><th>Contents</th></tr></thead><tbody>';
				footer += '</tbody></table>';
				for (var i = 0; i < count; i++) {
					var sdate = convertDateType(date_type,arrEvent[i].StartDate);
					var edate = convertDateType(date_type,arrEvent[i].EndDate);
					if (arrEvent[i].StartDate == arrEvent[i].EndDate) {
						var date = sdate;
					} else {
						var date = sdate+' - '+edate;
					}

					if (i < Math.ceil(count/2)) {
						h1 += '<tr><td class="date">'+date+'</td><td class="content">'+arrEvent[i].Content+'</td></tr>';
					} else {
						h2 += '<tr><td class="date">'+date+'</td><td class="content">'+arrEvent[i].Content+'</td></tr>';
					}
				}

				if(count == 1) {
					html = '<div class="row"><div class="col-md-12">'+head+h1+footer+'</div></div>';
				} else {
					html = '<div class="row"><div class="col-md-6 col-sm-6">'+head+h1+footer+'</div><div class="col-md-6 col-sm-6">'+head+h2+footer+'</div></div>';
				}
			}

			$('.print-detail-data').append(html);
		} else {
			arrEvent = <?php echo $calendars_events ?>;
			users = <?php echo $users ?>;
			arrday = $('.fc-center').text().split(' ');
			$('.list-year').remove();

			if(arrday.length != 8) {
				date = new Date(arrday[5],(Number(arrday[4].split(',')[0])-1),arrday[0]);
			} else {
				date = new Date(arrday[7],(Number(arrday[2].split(',')[0])-1),arrday[0]);
			}
			var daystart = new Date(Date.UTC(date.getFullYear(),(date.getMonth()) >= 10 ? (date.getMonth()) : ('0' + (date.getMonth()+1)),(date.getDate()) >= 10 ? (date.getDate()) : ('0' + (date.getDate()))));
			var dayend = new Date(Date.UTC(date.getFullYear(),(date.getMonth()) >= 10 ? (date.getMonth()) : ('0' + (date.getMonth())),(date.getDate()+6) >= 10 ? (date.getDate()+6) : ('0' + (date.getDate()+6))));
			var count = arrEvent.length;

			if (count >= 0) {
				html += '<table class="table table-bordered table-hover">';
				html += ' <thead class="thead-default">';
				html += '<th>Ngày</th>';
				html += '<th>Thời gian</th>';
				html += '<th>Nội dung công việc</th>';
				html += '<th class="width10">Thành phần</th>';
				html += '<th class="width10">Chủ trì</th>';
				html += '</thead>';
				html += '<tbody>';
				var arrRowId = {};
				for (var i = 0; i < 7; i++) {
					var arday = new Date(Date.UTC(date.getFullYear(),(date.getMonth()) >= 10 ? (date.getMonth()) : ('0' + (date.getMonth())),(date.getDate()+i) >= 10 ? (date.getDate()+i) : ('0' + (date.getDate()+i))));
					day = arday.toString().split(' ');
					html += '<tr>';

					var strId = 'td-'+i;
					var dateday1 = (arday.getDate()+i) >= 10 ? (date.getDate()+i) : ('0' + (arday.getDate()+i));
					var datemon1 = (arday.getMonth()+1) >= 10 ? (arday.getMonth()+1) : ('0' + (arday.getMonth()+1));
					var dateyear1 = arday.getFullYear();
					if(date_type != 1) {
						html += '<td class="dayth" id="'+strId+'" rowspan="" style="padding:10px">'+day[0]+' '+dateday1+'/'+datemon1+'/'+dateyear1+' '+'</td>';
					} else {
						html += '<td class="dayth" id="'+strId+'" rowspan="" style="padding:10px">'+day[0]+' '+dateyear1+' 年 '+datemon1+' 月 '+dateday1+' 日 '+'</td>';
					}
					var rowspan = 0;
					var olđay = arday;
					for (var j = 0; j < count; j++) {
						if(arrEvent[j]['DataKey'] == 'H') {
							Participant = arrEvent[j].Participant.split(',');
							checkuser = true;
						} else {
							Participant = [];
							checkuser = false;
						}
						if((Date.parse(new Date(arrEvent[j].StartDate)) == Date.parse(arday) && arrEvent[j].DataKey=='H')||(Date.parse(new Date(arrEvent[j].StartDate)) <= Date.parse(arday) && Date.parse(new Date(arrEvent[j].EndDate)) >= Date.parse(arday))) {
							if(Date.parse(arday) == Date.parse(olđay)) {
								rowspan = rowspan+1;
							}
							if(rowspan != 0 && rowspan != 1) {
								html += '<tr>';
							}
							if(!!!arrEvent[j]['MeetingTimeFrom']) {
								html += '<td>Cả ngày</td>';
							} else {
								html += '<td>'+arrEvent[j].MeetingTimeFrom+' đến '+arrEvent[j].MeetingTimeTo+'</td>';
							}
							html += '<td>'+arrEvent[j].Content+'</td>';
							if(checkuser) {
								html += '<td>';
								for (var h = 0; h < Participant.length; h++) {
									for (var k = 0; k < users.length; k++) {
										if(Participant[h] == users[k]['id']) {
											html += users[k]['FullName']+',';
										}
									}
								}
								html += '</td>';
								for (var l = 0; l < users.length; l++) {
									if(arrEvent[j].MeetingHostID == users[l]['id']) {
										html += '<td>'+users[l]['FullName']+'</td>';
									}
								}
							} else {
								html += '<td></td>';
								html += '<td></td>';
							}
						}
					}
					arrRowId[strId] = rowspan;
				}
				html += '</tr>';
				html += '</tbody>';
				$('#year-title').append(html);
				if(arrRowId !== undefined || arrRowId.length != 0) {
					$.each(arrRowId, function(i, e) {
						if(e > 0) {
							$('td#'+i).attr("rowspan", e);
						} else {
							$('td#'+i).parents('tr').append('<td></td><td></td><td></td><td></td>');
						}
						$('#year-title').css({
							"font-size": '30px',
						});
					});
				}
			}
			$('.print-detail-data').empty();
		}
		$('.header .title h2').hide();

	}

	function printMonthOfYear(date_type,yy) {
		var canchi = getYearCanChi(yy);
		var res = "";
		// res += printStyle();
		res += '\n';
		var strTitle = $( "#select-calendar option:selected" ).attr('data-title');
		for (var i = 1; i<= 12; i++) {
			if(i<10) {
				var mm = '0'+i;
			} else mm = i;

			if (date_type == 0) {
				title = strTitle+' ('+mm+'/'+yy+')';
			} else {
				title = strTitle+' ('+yy+'年'+mm+'月)';
			}

			var detail_data = printDataByMonth(date_type,yy,mm);

			res += '<div class="single-month"><div class="header"><div class="title"><h1 id="title">'+title+'</h1></div><div class="hr"></div></div><div class="row"><div class="col-md-12 detail-month">\n';
			res += printTable(i, yy);
			res += '</div></div>'+detail_data+'</div>\n';
		}
		res += printFoot();
		var d= window.location.href;
		var r = d.replace('admin/Calendar','js/amlich-hnd.js');
		var t = res.replace('amlich-hnd.js',r);
		return t;
	}

	function printDataByMonth(date_type,yy,mm) {
		var arrEvent = new Array();
		var idCalendar = $('#select-calendar option:selected').val().trim();
		arrEvent = <?php echo $calendars_events ?>;
		arrEvents = <?php echo $users ?>;
		var html = '',head = '', h1 = '', h2 = '', footer = '';
		var count = arrEvent.length;
		var counts = arrEvents.length;
		if (count > 0) {
			head += '<table class="table table-bordered"><thead><tr><th>Date</th><th>Contents</th></tr></thead><tbody>';
			footer += '</tbody></table>';
			for (var i = 0; i < count; i++) {
				var date = '';
				if(arrEvent[i].StartDate.substring(5,7) == mm ) {
					if (arrEvent[i].StartDate == arrEvent[i].EndDate) {
						date = convertDateType(date_type,arrEvent[i].StartDate);
					} else {
						date = convertDateType(date_type,arrEvent[i].StartDate)+' - '+convertDateType(date_type,arrEvent[i].EndDate)
					}
					if (i < Math.ceil(count/2)) {
						h1 += '<tr><td class="date">'+date+'</td><td class="content">'+arrEvent[i].Content+'</td></tr>';
					} else {
						h2 += '<tr><td class="date">'+date+'</td><td class="content">'+arrEvent[i].Content+'</td></tr>';
					}
				}
			}

			for (var j = 0; j < counts; j++) {
				var date1 = '';
				date1 = convertDateType(date_type,arrEvents[j].Birthday);
				if(!!!arrEvents[j].Birthday) {

				} else {
					if(arrEvents[j].Birthday.substring(5,7) == mm ) {
						if (j < Math.ceil(counts/2)) {
							h1 += '<tr><td class="date">'+date1+'</td><td class="content"> Sinh nhật '+arrEvents[j].FullName+'</td></tr>';
						} else {
							h2 += '<tr><td class="date">'+date1+'</td><td class="content"> Sinh nhật '+arrEvents[j].FullName+'</td></tr>';
						}
					}
				}

			}
			if(h1 != '' || h2 != '') {
				if(count == 1) {
					html = '<div class="detail_data"><<div class="row"><div class="col-md-12">'+head+h1+footer+'</div></div></div>';
				} else {
					html = '<div class="detail_data"><div class="row"><div class="col-md-6 col-sm-6">'+head+h1+footer+'</div><div class="col-md-6 col-sm-6">'+head+h2+footer+'</div></div></div>';
				}
			} else {
				html = '';
			}

		}
		return html;
	}

	// Edit: Tien 2020-04-24
	function weekCalendar(year) {
		$('#list-year').css({
			'max-width': '900px',
			margin: '0px auto',
			background: 'white'
		});

		// $('div.btn-expand').remove();
		$('div.hr').remove();
		$('#content').css('margin-top', '0px');

		var now = new Date();
		var moth = now.getMonth()+1;
		var day = now.getDate();
		var test = year + '-' + (moth >= 10 ? moth : ('0' + moth)) + '-' +(day >= 10 ? day : ('0' + day));
		var calendarEl = document.getElementById('list-year');
		var arrEvent = <?php echo $calendars_events ?>;
		var arrayeEents = [];
		for (var i = 0; i < arrEvent.length; i++) {
			var arrEndDate = new Date(arrEvent[i]['EndDate']);
			var lastDate     = new Date(arrEndDate.getFullYear(), arrEndDate.getMonth()+1, 0);
	   		var lastDay      = lastDate.getDate();
	   		if(arrEndDate.getDate() < lastDay){
	   			var day1 = arrEndDate.getDate()+1;
	   			var moth1 = arrEndDate.getMonth()+1;
	   		}else{
	   			var day1 = 1;
	   			var moth1 = arrEndDate.getMonth()+2;
	   		}
			var EndDate = arrEndDate.getFullYear() + '-' + (moth1 >= 10 ? moth1 : ('0' + moth1)) + '-' +(day1 >= 10 ? day1 : ('0' + day1));
			if(!!!arrEvent[i]['MeetingTimeFrom']) {
				arrayeEents.push({
					"id":''+arrEvent[i]['id']+'',
					"groupId":''+arrEvent[i]['DataKey']+'',
					"title":''+arrEvent[i]['Content']+'',
					"start": ''+arrEvent[i]['StartDate']+'',
					"end": ''+EndDate+''
				});
			} else {
				arrayeEents.push({
					"id":''+arrEvent[i]['id']+'',
					"groupId":''+arrEvent[i]['DataKey']+'',
					"title":''+arrEvent[i]['Content']+'',
					"start": ''+arrEvent[i]['StartDate']+'T'+arrEvent[i]['MeetingTimeFrom']+'',
					"end": ''+arrEvent[i]['EndDate']+'T'+arrEvent[i]['MeetingTimeTo']+'',
					color: '#257e4a'
				});
			}
		}
		let clickCnt = 0;
		var calendar = new FullCalendar.Calendar(calendarEl, {
			plugins: [ 'timeGrid', 'dayGrid', 'list' ],
			timeZone: 'UTC',
			defaultView: "<?php if(isset($_SESSION['calendarchanger']) && $_SESSION['calendarchanger'] == 1) { echo 'dayGridMonth'; } else { echo 'timeGridWeek'; } ?>",
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
			},
			locale: 'vi',
			editable: false,
			weekNumbers: true,
			minTime: '06:00:00',
			maxTime: '22:59:00',
			events: arrayeEents,
			eventRender: function (info) {
		      info.el.addEventListener('click', function() {
				    clickCnt++;
		        if (clickCnt === 1) {
		            oneClickTimer = setTimeout(function() {
		                clickCnt = 0;
		                var ajaxUrl1 = "{{ route('admin.CalendarItemWeek') }}";
						var calendar = info.event.groupId;
						var id =info.event.id;
						var title = info.event.title;
						var start =info.event.start.toISOString().split('T')[0];
						var end = info.event.end.toISOString().split('T')[0];
						var timeStart = info.event.start.toISOString().split('T')[1].split('Z')[0];
						var timeEnd = info.event.end.toISOString().split('T')[1].split('Z')[0];
						var search = '?ID='+id+'&Title='+title+'&start='+start+'&end='+end+'&timeStart='+timeStart+'&timeEnd='+timeEnd+'&C='+calendar;
							ajaxServer(ajaxUrl1+'/'+search, 'get', null, function (data) {
							$('#popupModal').empty().html(data);
							$('.detail-modal').modal('show');
						})
		            }, 400);
		        } else if (clickCnt === 2) {
		            clearTimeout(oneClickTimer);
		            clickCnt = 0;
		            var ajaxUrl1 = "{{ route('admin.CalendarItemWeek') }}";
					var calendar = info.event.groupId;
					var id =info.event.id;
					var title = info.event.title;
					var start =info.event.start.toISOString().split('T')[0];
					var end = info.event.end.toISOString().split('T')[0];
					var timeStart = info.event.start.toISOString().split('T')[1].split('Z')[0];
					var timeEnd = info.event.end.toISOString().split('T')[1].split('Z')[0];
					var search = '?ID='+id+'&Title='+title+'&start='+start+'&end='+end+'&timeStart='+timeStart+'&timeEnd='+timeEnd+'&C='+calendar;
						ajaxServer(ajaxUrl1+'/'+search, 'get', null, function (data) {
						$('#popupModal').empty().html(data);
						$('.detail-modal').modal('show');
					})

		        }

		      });
		    }
		});
		calendar.render();
	}
</script>
@endsection
