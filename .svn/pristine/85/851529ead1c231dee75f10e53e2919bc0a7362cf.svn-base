@component('admin.component.table')
@slot('columnsTable')
<tr>
    <th>@lang('admin.stt')</th>
    <th>@lang('admin.interview.name-job')</th>
    <th>@lang('admin.project.start_date')</th>
    <th>@lang('admin.project.end_date')</th>
    <th>@lang('admin.interview.number-applicant')</th>
    <th>@lang('admin.interview.status')</th>
    <th>@lang('admin.interview.last-update')</th>
    <th>@lang('admin.action')</th>
</tr>
@endslot
@if($interviewJobs->isNotEmpty())
@php
$temp = 0;
@endphp
@slot('dataTable')
@foreach($interviewJobs as $interviewJob)
@php
$temp++;
@endphp
<tr class="even gradeC" data-id="">
    <td class="text-center" width="5%">{{ $temp }}</td>
    <td class="text-left" width="30%">{{ $interviewJob->name }}</td>
    <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->start_date,'d/m/Y') }}</td>
    <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->end_date,'d/m/Y') }}</td>
    <td class="text-center" width="10%">
        {{ $interviewJob->num_candides }}
    </td>
    @if($interviewJob->active == 1)
    <td class="text-center" width="5%">
        <input type="checkbox" id="check_active" class="checkActive" data-job="{{ $interviewJob->id }}"
            value="{{ $interviewJob->active }}" checked>
    </td>
    @else
    <td class="text-center" width="10%">
        <input type="checkbox" id="check_active" class="checkActive" data-job="{{ $interviewJob->id }}"
            value="{{ $interviewJob->active }}">
    </td>
    @endif
    <td class="text-center" width="10%">{{ FomatDateDisplay($interviewJob->updated_at,'d/m/Y H:i') }}</td>
    <td class="text-center" width="10%">
        <a href="{{ route('admin.candidates.list',$interviewJob->id) }}" class="action-col update edit inter-view"
            data-job="{{ $interviewJob->id }}"><i class="fa fa-address-book-o" aria-hidden="true"></i></a>
        @can('action',$edit)
        <span class="action-col update edit edit_job" data-job="{{ $interviewJob->id }}"><i
                class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
        @endcan
        @can('action',$delete)
        <span class="action-col update delete delete_job" rmb-id="0" data-job="{{ $interviewJob->id }}"><i
                class="fa fa-times" aria-hidden="true"></i></span>
        @endcan
    </td>
</tr>
@endforeach
@endslot
@slot('pageTable')
{{ $interviewJobs->links() }}
@endslot
@else
<tr class="even gradeC">
    <td class='col-7'>Chưa có dữ liệu</td>
</tr>
@endif
@endcomponent
<div id="popupModal"></div>
