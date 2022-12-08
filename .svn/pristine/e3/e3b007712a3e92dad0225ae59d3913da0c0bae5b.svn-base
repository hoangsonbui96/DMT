<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tài liệu</title>
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        line-height: 1.6;
        font-family: DejaVu Sans, serif;
    }

    body {
        padding: 15px;
    }

    section {
        padding: 10px 0;
        margin: 30px 0;
    }

    p {
        margin-bottom: 10px;
    }
</style>
<body>
<header style="margin-top: 40px; font-family: DejaVu Sans, serif !important;">
    <h2 style="text-align: center; font-weight: 600;">{{ $title }}</h2>
</header>
<section>
    <div style="padding-left: 40px; padding-right: 40px;">
        <p><b>Tiêu đề:&nbsp;</b>{{ $meeting_name  }}.</p>
        @if(isset($time))
            <p><b>Thời gian:&nbsp;</b>{{ $time }}.</p>
        @endif
        <p><b>Người nhận báo cáo:&nbsp;</b>{{ $chair }}.</p>
        <p><b>Thành viên tham gia:&nbsp;</b>{{ $participant }}.</p>
        <p><b>Nhận xét chung:&nbsp;</b></p>
        <article>
            {!! $evaluation !!}
            @foreach($content_public as $content)
                @if (isset($content->Note_report))
                <div style="border-top: 0.1rem solid #ccc !important; margin: 15px 0"></div>
                <b>Nhận xét báo cáo của {{ $content->FullName }}:</b>
                    {!! $content->Note_report !!}
                @endif
            @endforeach
        </article>
    </div>
</section>
<footer></footer>
</body>
</html>
