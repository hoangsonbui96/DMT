<div class="table-responsive table-timekeeping-detail">
	<table class="table table-striped table-bordered table-hover data-table">
		<thead class="thead-default">
	        <tr>
        		<th colspan="2"><b>@lang('admin.equipment.Qr_code')</b></th>
        		<th><b>@lang('admin.equipment.code')</b></th>
        	</tr>
		</thead>
		<tbody>
			<?php
				$id = 1;
				$num = 0;
			?>
			@foreach($Equipments as $Equipment)
			<?php $num++ ?>
			@if($num == 1)
			<tr>
				<td colspan="2"></td>
				<td>{{ $Equipment->code }}</td>
			</tr>
			@else
				@for($i=0 ; $i<4;$i++)
					<tr></tr>
				@endfor
				<tr>
					<td colspan="2"></td>
					<td>{{ $Equipment->code }}</td>
				</tr>
			@endif
			@endforeach
		</tbody>
	</table>
</div>
