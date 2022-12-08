<div class="table-responsive table-timekeeping-detail">
    <table class="table table-striped table-bordered table-hover data-table">
        <thead class="thead-default">
        	<tr>
	            <th colspan="10">
	                BÀN GIAO  TRANG THIẾT BỊ
	            </th>
	        </tr>
	        <tr>
	            <th colspan="10">
	                Ngày.........Tháng.........Năm.........
	            </th>
	        </tr>
	        <tr>
	        	<th colspan="10"></th>
	        </tr>
	        <tr>
	        	<td style="width: 5px;">@lang('admin.stt')</td>
	        	<td colspan="2">@lang('admin.timekeeping.device-name')</td>
	        	<td style="width: 5px;">SL</td>
	        	<td style="width: 20px;">@lang('admin.equipment.serial_number')</td>
	        	<td style="width: 20px;">@lang('admin.equipment.Handover_user')</td>
	        	<td style="width: 20px;">@lang('admin.equipment.The_handover_user')</td>
	        	<td style="width: 15px;">@lang('admin.equipment.deal_date')</td>
	        	<td style="width: 20px;">@lang('admin.equipment.room_id')</td>
	        	<td>@lang('admin.absence.remark')</td>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php $num=1;?>
	    	@foreach($data as $row)
		    	<tr>
		    		<td>{{$num++}}</td>
		    		<td style="width: 15px;">{{$row->code}}</td>
		    		<td style="width: 15px;">{{$row->eqName}}</td>
		    		<td>1</td>
		    		<td>{{$row->serial_number}}</td>
		    		<td>{{$row->oldOwnerName}}</td>
		    		<td>{{$row->ownerName}}</td>
		    		<td>{{FomatDateDisplay($row->deal_date, FOMAT_DISPLAY_DAY)}}</td>
		    		<td>{{$row->room}}</td>
		    		<td>{{$row->note}}</td>
		    	</tr>
	    	@endforeach
	    	<tr>
	    		<td></td>
	    		<td colspan="2">Tổng số thiết bị</td>
	    		<td>=SUM(D5:D{{$intTotalRow}})</td>
	    		<td colspan="6"></td>
	    	</tr>
	    </tbody>
	</table>
	<br/>
	<br/>
	<table>
		<thead>
			<tr>
				<td></td>
				<td colspan="2">Người bàn giao</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="2">Người nhận bàn giao</td>
				<td></td>
			</tr>
		</thead>
	</table>
</div>