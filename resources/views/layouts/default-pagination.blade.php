<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><a class="page-link show-spinner">{{__('Previous')}}</a></li>
        @else
            <li class="page-item"><a class="page-link show-spinner" href="{{$paginator->previousPageUrl()}}">{{__('Previous')}}</a></li>
        @endif
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link show-spinner" href="{{$paginator->nextPageUrl()}}">{{__('see more')}}</a></li>
        @else
            <li class="page-item disabled"><a class="page-link show-spinner">{{__('see more')}}</a></li>
        @endif
    </ul>
</nav>
<div class="text-center">
    <p>{{__('Page')}}&nbsp;{{$paginator->currentPage()}}&nbsp;{{__('of')}}&nbsp;{{$paginator->lastPage()}}</p>
</div>
