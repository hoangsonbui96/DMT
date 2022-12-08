 <div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable "style="width: 400px !important;">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.calendar.copy')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label col-sm-2" id="title-year" style="float: none;width: auto;">Sao chép năm {{ $itemInfo }} đến năm <label id="yearcopy" > </label></label>
                            <div class="input-group date datetime_txtBox datetime_txtBox_overtime" id="year-copy" style="float: none;">
	                                <input type="text" class="form-control" id="year-copy-input"  name="year" value="{{ $itemInfo }}">
	                                <span class="input-group-addon">
	                                    <span class="glyphicon glyphicon-calendar"></span>
	                                </span>
	                                <input type="hidden" class="form-control" id="yearcopy-input"  name='yearcopy' value="{{ $itemInfo }}">
	                                <input type="hidden" class="form-control" id="select-calendarid"  name='select-calendar' value="{{ $CalendarID }}">
	                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                     @if(isset($itemInfo->Name))
                    <input type="hidden" name="id" value="{{ $itemInfo }}">
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
	$(function () {
        $('#year-copy-input').datetimepicker({
            format: 'YYYY',
        });
        $( ".ui-draggable" ).draggable();
        $(".selectpicker").selectpicker();
    });
	$(document).ready(function(){
		$('#yearcopy').append(<?php echo $itemInfo ?>);
		$('#year-copy-input').focusout(function(event) {
			yearchange = $('#year-copy-input').val();
			$('#yearcopy').remove();
			html ='<label id="yearcopy"> </label>';
			$('#title-year').append(html);
			$('#yearcopy').append(yearchange);
		});

		$('.save-form').click(function () {
	        $('.loadajax').show();
	        ajaxServer("{{ route('admin.Calendar') }}", 'post',  $('.detail-form').serializeArray(), function (data) {
	            if (typeof data.errors !== 'undefined'){
	                $('.loadajax').hide();
	                showErrors(data.errors[0]);
	            }else{
	                $('.loadajax').hide();
	                window.location.reload();
	            }
	        })
	    });
	});

</script>
