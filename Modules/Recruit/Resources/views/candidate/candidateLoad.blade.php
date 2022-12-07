@php $temp = 0 @endphp
@foreach($candidates as $candidate)
@php
$temp++;
@endphp
<tr class="even gradeC" data-id="">
    <td class="text-center" style="width: 20px;">{{ $temp }}</td>
    <td class="text-left" style="width: 150px;">{{ $candidate->FullName }}</td>
    <td class="text-center" style="width: 80px;">{{ $candidate->Tel }}</td>
    <td class="text-left" style="width: 180px;">
        {{ $candidate->Email }}
    </td>
    @if ($interviewJob == null)
        <td class="text-left" style="width: 150px;">{{ $candidate->Name }}</td>
    @endif
    <td class="text-center" style="width: 50px;">{{ $candidate->Experience }}</td>
    <td class="text-center" style="width: 90px;">
        @if($candidate->Status == null) chưa xử lý @endif
        @if($candidate->Status == 1 && $candidate->Approve == null) {{ FomatDateDisplay($candidate->InterviewDate,'d/m/Y H:i') }} @endif
        @if($candidate->Status == 1 && $candidate->Approve == 1) Thông qua phỏng vấn @endif
        @if($candidate->Status == 1 && $candidate->Approve == 2) Trượt phỏng vấn @endif
        @if($candidate->Status == 2) không<br>phỏng vấn @endif
    </td>
    <td class="text-left width12">
        {{ $candidate->Note }}
    </td>
    <td class="text-left width12">
        {{ $candidate->Evaluate }}
    </td>
    <td class="text-center" style="width: 90px;">
        {{ FomatDateDisplay($candidate->updated_at,'d/m/Y H:i') }}
    </td>
    <td class="text-center" @if($interviewJob == null)style="width: 140px;" @else style="width: 110px;" @endif>
        <a href="{{ route('admin.candidates.show_cv', [$candidate->JobID,$candidate->id]) }}" class="popup{{ $candidate->id }}"></a>
        <span class="action-col show_cv" data-file="{{ $candidate->CVpath }}" data-candidate="{{ $candidate->id }}">
            <i class="fa fa-eye" aria-hidden="true"></i></span>
        @can('action',$add)
        @if($candidate->InterviewDate == null)
        <span class="action-col update edit add_interview" data-job="{{ $candidate->JobID }}"
            data-candidate="{{ $candidate->id }}" data-candidate-status = {{ $candidate->Status }}>
            <i class="fa fa-calendar-plus-o" aria-hidden="true"></i></span>
        @else
        <span class="action-col update edit edit_interview" data-interview="{{ $candidate->interview_id }}"
            data-job="{{ $candidate->JobID }}" data-candidate="{{ $candidate->id }}"><i class="fa fa-calendar-minus-o"
                aria-hidden="true"></i></span>
        @endif
        @endcan
        <span class="action-col download_cv" data-file="{{ $candidate->CVpath }}"><i class="fa fa-download"></i></span>
        @can('action',$edit)
        <span class="action-col update edit edit_candidate" data-job="{{ $candidate->JobID }}"
            data-candidate="{{ $candidate->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
        @endcan
        @can('action',$delete)
        <span class="action-col update delete delete_candidate" data-job="{{ $candidate->JobID }}"
            data-candidate="{{ $candidate->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
        @endcan
    </td>
</tr>
@endforeach
