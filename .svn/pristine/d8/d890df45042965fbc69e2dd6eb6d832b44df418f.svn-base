@extends('admin.layouts.default.app')
@section('content')
<style>
    html, body {margin: 0; height: 100%; overflow: hidden}
</style>
<section class="content-header">
    <h1 class="page-header">@lang('document::admin.list-document')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <form action="">
                <select class="selectpicker" data-live-search="true" data-size="5" id="select-document"
                        data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                    @foreach($arrDocument as $key => $item)
                    <optgroup label="{{ $item['parent']->Name }}">
                        @foreach($item['children'] as $doc)
                            <option data-file="{{$doc->fileName}}" data-url="{{$doc->dUrl}}" data-check="{{$doc->check}}">{{$doc->dName}}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="col-sm-12">
            <div class="tab-content">
                <div class="tab-pane active" id="classIframe">
                    <h3><iframe src="" width="100%"
                                style="height:85vh"
                                id="iframe"></iframe></h3>
                    <h3><iframe src="" width="100%" style="height:85vh; pointer-events: none;" id="iframe"></iframe></h3>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script !src="">

        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        }, false);

        document.addEventListener("copy", function(evt){
            // Change the copied text if you want
            evt.clipboardData.setData("text/plain", "Copying is not allowed on this webpage");

            // Prevent the default copy action
            evt.preventDefault();
        }, false);

        document.onkeydown = function(e) {
            if(event.keyCode == 123) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                return false;
            }
        }

        var routeDownloadDoc = "{{ route('admin.routeDownloadDoc') }}?path=";

        $(function () {
            funcViewIframe();
        });
        $('#select-document').change(function () {
            funcViewIframe();
        });
        function funcViewIframe() {
            var pathFile = $('#select-document :selected').attr('data-file');
            var url = $('#select-document :selected').attr('data-url');
            var check = $('#select-document :selected').attr('data-check');
            let host = "{{ asset('storage/app/public/files/shares') }}";
            // let link_file = window.location.protocol + "//" + window.location.host + "/" + pathFile;
            let link_file = "{{ asset('') }}" + "/"+ pathFile + "#toolbar=0";
            if(pathFile != '' && pathFile != 'undefined' && check != 'InfoExtensionNotOk'){
                $('#iframe').show();
                $('#classIframe p').empty();
		        // $('#iframe').attr('src',  'https://drive.google.com/file/d/1odSyXFfM-AJncUWMfFL8SuXBQm63sThL/preview');
               $('#iframe').attr('src',  link_file);
            }
            if (url != '' && url != 'undefined' && url != 'false' ){
                $('#classIframe p').empty();
                $('#iframe').attr('src', url);
            }
            if(check == 'fileFalse'){
                $('#iframe').hide();
                $('#classIframe p').empty();
                $('#classIframe').append("<p style='color: red'>Tài liệu không tồn tại!</p>");
            }
            if(check == 'InfoExtensionNotOk'){
                $('#iframe').hide();
                $('#classIframe p').empty();
                $('#classIframe').append("<p style='color: red'>Tài liệu không thể hiển thị tải tài liệu: " + "<a href='"+routeDownloadDoc+pathFile+"'>Tại đây</a></p>");
            }
            if(check == 'false'){
                $('#iframe').hide();
                $('#classIframe p').empty();
                $('#classIframe').append("<p style='color: red'>Không thể hiển thị tài liệu vui lòng truy cập đường dẫn: <a href='"+url+"' target='_blank'>Tại đây</a> </p>");
            }
            if(check == 'urlNotFound'){
                $('#iframe').hide();
                $('#classIframe p').empty();
                $('#classIframe').append("<p style='color: red'>Đường dẫn không tồn tại, vui lòng kiểm tra lại.</p>");
            }
        }
    </script>
@endsection

