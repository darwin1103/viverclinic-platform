<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">{{__('Previous')}}</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">{{__('Previous')}}</a>
            </li>
        @endif
        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            @if ($page == $paginator->currentPage())
                <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                </li>
            @endif
        @endfor
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">{{__('Next')}}</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">{{__('Next')}}</span>
            </li>
        @endif
    </ul>
</nav>
