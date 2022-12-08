<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="{{ asset('imgs/compary-icon.ico') }}">

    <style>
        body {
            background-color: rgb(82, 86, 89);
        }

        canvas {
            border: 1px solid black;
            direction: ltr;
        }

        #pdfViewer {
            text-align: center;
        }
    </style>

    <style type="text/css" media="print">
        body {
            display: none;
            visibility: hidden;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"
        integrity="sha512-jGR1T3dQerLCSm/IGEGbndPwzszJBlKQ5Br9vuB0Pw2iyxOy+7AK+lJcCC8eaXyz/9du+bkCy4HXxByhxkHf+w=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
</head>

<body oncontextmenu="return false;">
    <div id="pdfViewer"></div>
    <input id="urlPdf" type="hidden" value="{{ $url_document }}" />
    <script type="text/javascript">
        window.addEventListener("contextmenu", e => e.preventDefault());
        document.onkeydown = e => false;

        function LoadPagePdf(pdf, intPage) {
            if (!pdf || !pdf.numPages) return;
            if (intPage > pdf.numPages) return;

            intPage = intPage > 1 ? intPage : 1;

            pdf.getPage(intPage).then(function (page) {
                var scale = 1.5;
                var viewport = page.getViewport({
                    scale: scale
                });
                var pdfViewer = $('#pdfViewer');
                var idPageViewer = 'pageViewer' + pageNumber;

                pdfViewer.append(`<canvas id="${idPageViewer}" oncontextmenu="return false;"></canvas>`);

                // Prepare canvas using PDF page dimensions
                var canvas = document.getElementById(idPageViewer);
                var context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page into canvas context
                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                var renderTask = page.render(renderContext);
                renderTask.promise.then(function () {
                    pageNumber++;
                    LoadPagePdf(pdf, pageNumber);
                });
            });
        }
        

        // If absolute URL from the remote server is provided, configure the CORS
        // header on that server.
        var url = $('#urlPdf').val();
        $('#urlPdf').remove();

        if (window.opener) {
            // Loaded via <script> tag, create shortcut to access PDF.js exports.
            var pdfjsLib = window['pdfjs-dist/build/pdf'];

            // The workerSrc property shall be specified.
            // pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

            // Asynchronous download of PDF
            var loadingTask = pdfjsLib.getDocument(url);
            var pageNumber = 1;

            loadingTask.promise.then(function (pdf) {
                LoadPagePdf(pdf, pageNumber);
            }, function (reason) {
                // PDF loading error
                alert(reason);
            });
        }

        $('script').remove();
    </script>
</body>
</html>