@foreach ($projects as $key => $item)
    <tr id="project{{ $item->id }}">
        <td style="text-align: center">
            {{ ($projects->currentPage() - 1) * $projects->perPage() + $key + 1 }}
        </td>
        <td> {{ $item->NameVi }} </td>
        <td> {{ $item->NameShort }} </td>
        <td>
            <a style="color: black" id="showPhaseJob"
                href="{{ route('admin.showPhaseJob') }}?projectId={{ $item->id }}#tab_phase"
                project-id="{{ $item->id }}">
                <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                    {{ count($item->phases) }}
                </button>
            </a>
        </td>
        <td>
            <a style="color: black" id="showPhaseJob"
                href="{{ route('admin.showPhaseJob') }}?projectId={{ $item->id }}#tab_job"
                project-id="{{ $item->id }}">
                <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                    {{ count($item->jobs) }}
                </button>
            </a>
        </td>
        <td>
            <a style="color: black" id="showPhaseJob"
                href="{{ route('admin.showTasks') }}?projectId={{ $item->id }}"
                project-id="{{ $item->id }}">
                <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                    {{ count($item->tasks) }}
                </button>
            </a>
        </td>
        <td style="text-align: center">
            {{ $item->estimatedDuration }}
        </td>
        <td style="text-align: center">
            {{ $item->workedHours }}
        </td>
        <td style="text-align: center">
            {{ $item->OTDuration }}
        </td>

        @if ($item->hasUnscheduledTask)
            <td style="text-align: center" title="Tiến độ đang được được tính không bao gồm các Task chưa có Thời gian dự kiến">
                {{ $item->progress }} <span class="text-red">*</span>
            </td>
        @else
            <td style="text-align: center">
                {{ $item->progress }}
            </td>
        @endif
    <td>
        <a style="color: black" class="show-members-btn" project-id="{{ $item->id }}" href="javascript:void(0)">
            <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                {{ count($item->users) }}
            </button>
        </a>
    </td>
    <td> {{ $item->Customer }} </td>
    <td style="text-align: center"> {{ FomatDateDisplay($item->StartDate, FOMAT_DISPLAY_DAY) }} </td>
    <td style="text-align: center"> {{ FomatDateDisplay($item->EndDate, FOMAT_DISPLAY_DAY) }} </td>
    <td class="text-center">
        @if ($item->Active == 1)
            <span class="label label-success">{{ __('projectmanager::admin.Active') }}</span>
        @else
            <span class="label bg-gray disabled color-palette">{{ __('projectmanager::admin.Inactive') }}</span>
        @endif
    </td>
    <td class="text-center">
        <span class="action-col" item-id="{{ $item->id }}">
            <a class="showProgress" href="{{ route('admin.showProgress') }}?projectId={{ $item->id }}"
                project-id="{{ $item->id }}"><i class="fa fa-bar-chart" aria-hidden="true"
                    title="Xem tiến độ dự án"></i></a>
        </span>
        @if ($permissions['create'])
            <span class="action-col update-project" project-id="{{ $item->id }}">
                <i class="fa fa-pencil-square-o" aria-hidden="true" title="Chỉnh sửa"></i>
            </span>
            <span class="action-col delete-project" project-id="{{ $item->id }}">
                <i class="fa fa-times" aria-hidden="true" title="Xóa"></i>
            </span>
        @endif
    </td>
</tr>
@endforeach
