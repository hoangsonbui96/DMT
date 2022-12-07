<table>
    <thead>
    <tr>
        <th colspan="6" style="font-family:'Times New Roman'">@lang('admin.partner.partner_management')</th>
    </tr>
    <tr>
        <th ><b>@lang('admin.stt')</b></th>
        <th ><b>@lang('admin.partner.nameCompany')</b></th>
        <th ><b>@lang('admin.partner.InfoRepresentatives')</b></th>
        {{-- <th ><b>@lang('admin.partner.birthday')</b></th> --}}
        <th ><b>@lang('admin.partner.tel')</b></th>
        <th ><b>@lang('admin.email')</b></th>
        <th ><b>@lang('admin.partner.address')</b></th>
        {{-- <th ><b>@lang('admin.partner.sectors')</b></th> --}}

    </tr>
    </thead>
    <tbody>
    @foreach($partners as $partner)
        <tr>
            <td >{{ $loop->iteration }}</td>
            <td >{{ $partner->full_name }}</td>
            <td >{{ $partner->InfoRepresentatives }}</td>
            {{-- <td >{{ FomatDateDisplay($partner->birthday, FOMAT_DISPLAY_DAY) }}</td> --}}
            <td >{{ $partner->tel }}</td>
            <td >{{ $partner->email }}</td>
            <td >{{ $partner->address }}</td>
            {{-- <td >{{ $partner->sectors }}</td> --}}
        </tr>
    @endforeach
    </tbody>
</table>

