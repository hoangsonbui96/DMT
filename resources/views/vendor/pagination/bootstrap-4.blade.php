@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
                <li class="" id="previousPage">
                    <a class="page-link" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page" page-nums={{$page}}>
                                <a class="page-link" page-nums={{$page}}>{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item" page-nums={{$page}}>
                                <a class="page-link" id="page{{$page}}" href="{{ $url }}" page-nums={{$page}}>{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            {{-- @if ($paginator->hasMorePages()) --}}
                <li class="" id="nextPage">
                    <a class="page-link">&rsaquo;</a>
                </li>
            {{-- @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif --}}
        </ul>
    </nav>
@endif
