<div class="modal draggable fade in detail-modal modal-css" role="dialog" data-backdrop="static">
    <div class="modal-dialog ui-draggable" style="width: 60%">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="" style="word-break: break-word;">
                    @if(isset($error))
                        Báo lỗi 
                    @else 
                        Báo cáo hàng ngày 
                    @endif 
                    [<span class="modal-title"></span>]
                </h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" id="form-report" style="padding-top: 1em">
                    <div class="save-errors"></div>
                    <div class="tab-content">
                        @if(isset($error))
                            @include('projectmanager::includes.error-tab')
                        @else
                            @include('projectmanager::includes.report-tab')
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
