<div class="table-responsive table-timekeeping-detail">
	<table class="table table-striped table-bordered table-hover data-table">
		<thead class="thead-default">
			<tr>
	            <th colspan="13" style="text-align: center;" >
	                BẢNG KÊ CHI TIẾT TRANG THIẾT BỊ
	            </th>
	        </tr>
	        <tr>
            <th colspan="13" style="text-align: center;">
                <div style="text-align: center;">Ngày .. Tháng .. Năm ..</div>
            </th>
        	</tr>
        	<tr>
        		<th colspan="13">
	                <div style="text-align: center;"></div>
	            </th>
        	</tr>
        	<tr>
        		<th style="width:5px;"><b>@lang('admin.stt')</b></th>
        		<th colspan="2"style="width:25px;"><b>@lang('admin.equipment.name')</b></th>
        		<th style="width:5px;"><b>SL</b></th>
        		<th style="width:20px;"><b>@lang('admin.equipment.info')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.serial_number')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.Users')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.Date_of_delivery')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.provider')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.buy_date')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.period_date')</b></th>
        		<th style="width:15px;"><b>@lang('admin.equipment.room_id')</b></th>
        		<th style="width:20px;"><b>@lang('admin.note')</b></th>
        	</tr>
		</thead>
		<tbody>
			<?php
				$id = 1;
				$num = 0;
			?>
			@foreach($Equipments as $Equipment)
			<?php $num++ ?>
			<tr>
				<td><?php echo $id++ ?></td>
				<td>{{ $Equipment->code }}</td>
				<td>{{ $Equipment->name }}</td>
				<td>1</td>
				<td>{{ $Equipment->info }}</td>
				<td>{{ $Equipment->serial_number }}</td>
				@if($Equipment->user_owner == 0)
                  <td>Kho</td>
                @else
                @foreach($owners as $owner)
                	@if($owner->id == $Equipment->user_owner)
                    	<td>{{ $owner->FullName}}</td>
                  	@endif
                @endforeach   
                @endif
				<td>{{ FomatDateDisplay($Equipment->deal_date, FOMAT_DISPLAY_DAY)}}</td>
				<td>{{ $Equipment->provider }}</td>
				<td>{{ FomatDateDisplay($Equipment->buy_date, FOMAT_DISPLAY_DAY) }}</td>
				<td>{{ FomatDateDisplay($Equipment->period_date, FOMAT_DISPLAY_DAY) }}</td>
				@if($Equipment->room_id == '')
                  <td></td>
                @else
                @foreach($rooms as $room)
                	@if($room->id == $Equipment->room_id)
                    	<td>{{ $room->Name }}</td>
                  	@endif
                @endforeach   
                @endif
				<td>{{ $Equipment->note }}</td>
			</tr>
			@endforeach
			<tr>
				<td></td>
				<td class="thead-th-custom" colspan="2">Tổng số thiết bị</td>
				<td class="thead-th-custom">
					<?php echo $num ?>
				</td>
				<td colspan="9"></td>
			</tr>
			<!-- <tr>
				<td colspan="13"></td>
			</tr> -->
			<!-- <tr>
				<td colspan="9"></td>
				<td style="text-align: center;" colspan="3">Ngày .. tháng … năm</td>
			</tr>
			<tr>
				<td colspan="9"></td>
				<td style="text-align: center;" colspan="3">Người lập bảng ký</td>
			</tr> -->
		</tbody>
	</table>
	<br>
	<table class="table">
		<tr>
			<th colspan="10">
            </th>
            <th colspan="3" style='text-align: center;font-family: Times News Roman;'>
                <div style='text-align: center;'>Ngày .. tháng … năm</div>
            </th>
        </tr>
        <tr>
			<th colspan="10">
            </th>
            <th colspan="3" style='text-align: center;font-family: Times News Roman;'>
                <div style='text-align: center;'>Người lập bảng ký</div>
            </th>
        </tr>
	</table>
</div>