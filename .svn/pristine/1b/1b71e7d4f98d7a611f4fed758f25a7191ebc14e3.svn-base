<style>
    .flag-menu {
        width: 48px !important;
        background: #3c8dbc;
        min-width: 48px;
        text-align: center;
        border: 0px;
        padding: 6px 0px !important;
    }

    .flag-menu li {
        width: 100% !important;
        margin: -5px 0px;
    }

    .flag-menu li:hover {
        background: #0f5d8a !important;
        cursor: pointer;
    }
</style>
<header class="main-header">
    <!-- Logo -->
    <a href="http://www.akb.com.vn/vi/" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">AKB</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">AKB Software</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                {{-- flag icon --}}
                <li class="dropdown notifications-menu" style="display: none">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                       style="padding:0px 8px;height:50px;display: none;">
                        <img src="{{ asset('imgs/'.Config::get('app.locale').'.png') }}"
                             style="height:32px;margin-top: 10px;">

                    </a>
                    <ul class="dropdown-menu flag-menu">
                        @foreach($nameFolder as $item)
                            @if(Config::get('app.locale') != $item)
                                <li lang="{{$item}}"><img src="{{ asset('imgs/'.$item.'.png') }}" style="height:32px;">
                                </li>
                            @endif
                        @endforeach

                        {{--                        @if(Config::get('app.locale') != 'en')--}}

                        {{--                        <li lang="en"><img src="{{ asset('imgs/en.png') }}" style="height:32px;"></li>--}}
                        {{--                        @endif--}}
                        {{--                        @if(Config::get('app.locale') != 'ja')--}}
                        {{--                            <li lang="ja"><img src="{{ asset('imgs/ja.png') }}" style="height:32px;"></li>--}}
                        {{--                        @endif--}}
                        {{-- <li><img src="{{ asset('imgs/ja.png') }}" style="height:32px;"></li> --}}
                    </ul>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown">
                    <a href="javascript:void(0)" name="calendar-personal" class="dropdown-toggle">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </a>
                </li>
                <li class="dropdown notifications-menu" style="display: none">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-toggle="tooltip"
                       data-placement="top" title="Thông báo">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning total-notification">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have <span class="total-notification"></span> notifications</li>
                        <li class="body-notifications-menu">
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                {{-- <li>
                                    <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                                        page and may cause design problems
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users text-red"></i> 5 new members joined
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user text-red"></i> You changed your username
                                    </a>
                                </li> --}}
                            </ul>
                        </li>
                        {{-- <li class="footer"><a href="javascript:void(0)">View all</a></li> --}}
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!--<img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                        <img
                            src="{{ Auth::user()->avatar != '' ? url(Auth::user()->avatar) : asset('imgs/user-blank.jpg') }}"
                            onerror="this.src='{{ asset('imgs/user-blank.jpg') }}'" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Auth::user()->FullName }}</span>
                        &nbsp;
                        <i class="fa fa-gears"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img
                                src="{{ Auth::user()->avatar != '' ? url(Auth::user()->avatar) : asset('imgs/user-blank.jpg') }}"
                                onerror="this.src='{{ asset('imgs/user-blank.jpg') }}'" class="img-circle"
                                alt="User Image"/>
                            <p>
                            {{ Auth::user()->FullName }}
                            <!--<small>Member since Nov. 2012</small>-->
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <a href="{{ GetRouter('admin.viewLayoutChangePassword') }}">@lang('menu.changePassword')</a>
                                </div>
                            </div>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ GetRouter('admin.ProfileUser') }}"
                                   class="btn btn-default btn-flat">@lang('menu.profile')</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ GetRouter('logout') }}"
                                   class="btn btn-default btn-flat">@lang('menu.signOut')</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <div class="user-panel" style="background-color: #d2d6de">
            <img src="{{ asset('imgs/logo_com.png') }}" alt="AKB" id="logo-akb" width="100%">
        </div>
        <ul class="sidebar-menu" data-widget="tree">
            @foreach($mainMenus as $menu)
                @if($menu->childMenus->count() >0 && $controller->childMenuCanView($menu, \auth()->id()))
                    <li class="treeview">
                        <a href="{{ is_null($menu->RouteName) ? '#' : GetRouter($menu->RouteName) }}"
                           alias="{{ $menu->alias }}">
                            <i class="{{ $menu->FontAwesome }}"></i> <span>@lang('menu.'.$menu->LangKey)</span>
                            <span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($menu->childMenus as $menuItem)
                                @can('view', $menuItem)
                                    @if ($currentRouteName == $menuItem->RouteName)
                                        <li class="active"><a href="{{ GetRouter($menuItem->RouteName) }}"
                                                              onclick="$('.loadajax').show()"
                                                              alias="{{ $menuItem->alias }}"><i
                                                    class="fa fa-circle-o"></i> @lang('menu.'.$menuItem->LangKey)
                                            </a></li>
                                    @else
                                        <li><a href="{{ GetRouter($menuItem->RouteName) }}"
                                               onclick="$('.loadajax').show()" alias="{{ $menuItem->alias }}"><i
                                                    class="fa fa-circle-o"></i> @lang('menu.'.$menuItem->LangKey)
                                            </a></li>
                                    @endif
                                @endcan
                            @endforeach
                        </ul>
                    </li>
                @else
                    @can('view', $menu)
                        @if ($currentRouteName == $menu->RouteName)
                            <li class="active menu-open">
                        @else
                            <li>
                                @endif
                                <a href="{{ GetRouter($menu->RouteName) }}" onclick="$('.loadajax').show()"
                                   alias="{{ $menu->alias }}">
                                    <i class="{{ $menu->FontAwesome }}"></i>
                                    <span>@lang('menu.'.$menu->LangKey)</span>
                                </a>
                            </li>
                        @endcan
                    @endif
                    @endforeach
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<script type="text/javascript">
    const DAY_OF_WEEK = moment().day();
    let CURRENT_MONTH = '';
    let LIST_USER_BIRTHDAY = [];

    $('.sidebar-menu ul.treeview-menu li.active').parent().parent('li.treeview').addClass('active menu-open');

    $(function () {
        getAllNotification();

        $('li.treeview').on('click', function (e) {
            $('li.treeview > a:first-child > .pull-right-container > small.lbp').show(400, function () {
                $(this).next().hide();
            });
            if ($(this).hasClass('menu-open')) {
                $(this).find('a:eq(0) > .pull-right-container > small.lbp').show();
                if ($(this).find('a:eq(0) > .pull-right-container > small.lbp').length) {
                    $(this).find('a:eq(0) > .pull-right-container > .fa.pull-right').hide();
                }
            } else {
                $(this).find('a:eq(0) > .pull-right-container > small.lbp').hide();
                if ($(this).find('a:eq(0) > .pull-right-container > small.lbp').length) {
                    $(this).find('a:eq(0) > .pull-right-container > .fa.pull-right').show();
                }
            }
        });
        $('.flag-menu li').click(function () {
            var lang = $(this).attr('lang');
            $.ajax({
                url: "{{ route('ajax.setCookie') }}",
                type: 'post',
                data: {
                    lang: lang,
                },
                success: function (result) {
                    if (result == 1) {
                        window.location.reload();
                    }
                },
                fail: function (error) {
                    console.log(error);
                }
            });
        });
    });

    function showContent() {
        let html = ``;
        if (LIST_USER_BIRTHDAY.length > 0) {
            html += `<ol>`;
            $.each(LIST_USER_BIRTHDAY, function (i, e) {
                html += `
					<li>
						<b>${e.FullName}</b>
						<i>(${moment(e.Birthday).format('DD/MM/YYYY')})</i>
					</li>
				`;
            });
            html += `</ol>`;
        } else {
            html = `<b><i>Không có ai sinh nhật tháng này</i></b>`;
        }
        $.alert({
            title: `Danh sách sinh nhật tháng <b>${CURRENT_MONTH}</b>`,
            content: html,
            buttons: {
                close: {
                    text: "@lang('admin.btnCancel')",
                    action: function () {

                    }
                },
            }
        });
    }

    function getAllNotification() {
        switch (DAY_OF_WEEK) {
            case 1:
            case 2:
            case 3:
                dateNow = moment().format('YYYY-MM-DD');
                next1Day = moment().add(1, 'days').format('YYYY-MM-DD');
                next2Day = moment().add(2, 'days').format('YYYY-MM-DD');
                break;
            case 4:
                dateNow = moment().format('YYYY-MM-DD');
                next1Day = moment().add(1, 'days').format('YYYY-MM-DD');
                next2Day = moment().add(4, 'days').format('YYYY-MM-DD');
                break;
            case 5:
                dateNow = moment().format('YYYY-MM-DD');
                next1Day = moment().add(3, 'days').format('YYYY-MM-DD');
                next2Day = moment().add(4, 'days').format('YYYY-MM-DD');
                break;
            case 6:
                dateNow = moment().add(2, 'days').format('YYYY-MM-DD');
                next1Day = moment().add(3, 'days').format('YYYY-MM-DD');
                next2Day = moment().add(4, 'days').format('YYYY-MM-DD');
                break;
            case 0:
                dateNow = moment().add(1, 'days').format('YYYY-MM-DD');
                next1Day = moment().add(2, 'days').format('YYYY-MM-DD');
                next2Day = moment().add(3, 'days').format('YYYY-MM-DD');
                break;
        }

        var res = {};

        $.ajax({
            url: "{{ route('admin.getAllNotification') }}",
            type: 'get',
            data: {
                dateNow: dateNow,
                next2Day: next2Day
            },
            success: function (result) {
                // res = JSON.parse(result);
                res = result;
                let intTotalNotification = 0;

                let countUserBirthday = res.birthday.listData.length;
                CURRENT_MONTH = res.birthday.currentMonth;
                LIST_USER_BIRTHDAY = res.birthday.listData;
                if (countUserBirthday > 0) {
                    intTotalNotification += 1;
                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:void(0)" onclick="showContent()">
								<i class="fa fa-birthday-cake text-yellow" aria-hidden="true"></i> Có ${countUserBirthday} sinh nhật trong tháng
							</a>
						</li>
					`);
                }

                let countUserAbsent = res.absence.request.threeDays.count;
                if (countUserAbsent > 0) {
                    intTotalNotification += 1;
                    let uriString = `{{ GetRouter('admin.Absences') }}?`;
                    uriString += encodeURIComponent(`Date[]`) + '=' + encodeURIComponent(`${moment(dateNow).format('DD/MM/YYYY')}`) + '&';
                    uriString += encodeURIComponent(`Date[]`) + '=' + encodeURIComponent(`${moment(next2Day).format('DD/MM/YYYY')}`);
                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:locationPage('${uriString}')">
								<i class="fa fa-user-times text-red" aria-hidden="true"></i> Có ${countUserAbsent} người vắng mặt trong 3 ngày tới
							</a>
						</li>
					`);
                }

                let countUserRqAbsent = res.absence.request.all.count;
                if (countUserRqAbsent > 0) {
                    intTotalNotification += 1;
                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:locationPage('{{ GetRouter('admin.AbsencesListApprove') }}')">
								<i class="fa fa-flag-o text-green" aria-hidden="true"></i> Có ${countUserRqAbsent} đơn xin vắng mặt cần duyệt
							</a>
						</li>
					`);

                    $('a[alias=AbsenceListApprove]').append(`
						<span class="pull-right-container">
							<small class="label pull-right bg-green">${countUserRqAbsent}</small>
						</span>
					`);

                    let tempHTML = $.trim($('a[alias=AbsenceListApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html());
                    $('a[alias=AbsenceListApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html(`
						<small class="label pull-left bg-green lbp">${countUserRqAbsent}</small>${tempHTML}
					`);

                    if (!$('a[alias=AbsenceListApprove]').closest('ul').parent().hasClass('active')) {
                        $('a[alias=AbsenceListApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container > .fa.pull-right').hide();
                    }
                }

                let countRqOT = res.overtimes.request.count;
                if (countRqOT > 0) {
                    intTotalNotification += 1;
                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:locationPage('{{ GetRouter('admin.OvertimeListApprove') }}')">
								<i class="fa fa-clock-o text-blue" aria-hidden="true"></i> Có ${countRqOT} đơn giờ làm thêm cần duyệt
							</a>
						</li>
					`);

                    $('a[alias=OvertimeDetailsApprove]').append(`
						<span class="pull-right-container">
							<small class="label pull-right bg-blue">${countRqOT}</small>
						</span>
					`);

                    let tempHTML = $.trim($('a[alias=OvertimeDetailsApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html());
                    $('a[alias=OvertimeDetailsApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html(`
						<small class="label pull-left bg-blue lbp">${countRqOT}</small>${tempHTML}
					`);
                    if (!$('a[alias=OvertimeDetailsApprove]').closest('ul').parent().hasClass('active')) {
                        $('a[alias=OvertimeDetailsApprove]').closest('ul').parent().find('a:eq(0) > .pull-right-container > .fa.pull-right').hide();
                    }
                }

                let countEventActive = res.events.count;
                let listEventActive = res.events.listData;

                if (countEventActive > 0) {
                    intTotalNotification += 1;

                    let intEventNotVote = listEventActive.filter((value, index) => {
                        return value.StatusA === 0;
                    }).length;

                    let uriString = `{{ GetRouter('admin.Events') }}?`;
                    uriString += encodeURIComponent(`Date[]`) + '=' + encodeURIComponent(`${moment(dateNow).format('DD/MM/YYYY')}`) + '&';
                    uriString += encodeURIComponent(`Date[]`) + '=';

                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:locationPage('${uriString}')">
								<i class="fa fa-trophy text-red" aria-hidden="true"></i> Đang có ${countEventActive} sự kiện diễn ra
							</a>
						</li>
					`);

                    if (intEventNotVote > 0) {
                        $('a[alias=EventList]').append(`
							<span class="pull-right-container">
								<small class="label pull-right bg-red">${intEventNotVote}</small>
							</span>
						`);
                    }
                }

                let countDailyReport = res.dailyReports.count;

                if (countDailyReport > 0) {
                    intTotalNotification += 1;

                    let uriString = `{{ GetRouter('admin.NeedApproveReports') }}?`;

                    $('.body-notifications-menu .menu').append(`
						<li>
							<a href="javascript:locationPage('${uriString}')">
								<i class="fa fa-tasks text-red" aria-hidden="true"></i> Đang có ${countDailyReport} báo cáo hàng ngày chờ duyệt
							</a>
						</li>
					`);

                    $('a[alias=NeedApproveReports]').append(`
						<span class="pull-right-container">
							<small class="label pull-right bg-yellow">${countDailyReport}</small>
						</span>
					`);

                    let tempHTML = $.trim($('a[alias=NeedApproveReports]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html());
                    $('a[alias=NeedApproveReports]').closest('ul').parent().find('a:eq(0) > .pull-right-container').html(`
						<small class="label pull-left bg-yellow lbp">${countDailyReport}</small>${tempHTML}
					`);
                }

                $('li.treeview.active.menu-open a:eq(0) > .pull-right-container > small.lbp').hide();

                if (intTotalNotification > 0) {
                    $('.notifications-menu').show();
                    $('.total-notification').text(intTotalNotification);
                }
            },
            fail: function (error) {
                console.log(error);
            }
        });
    }

    $('a[name="calendar-personal"]').click(function () {
        document.cookie = 'showNoti=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        $('#noti-personal').empty();
        ajaxGetServerWithLoader("{{ route('admin.NotificationPersonal') }}", "GET", null, function (data) {
            $('#noti-personal').html(data);
        });
    });
</script>
