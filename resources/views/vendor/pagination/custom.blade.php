<style>
    .pagination li a,
.pagination li span {
    color: #000000;
    background-color: #b8b5b6;
    border: 1px solid #f0f0f0;
    padding: 8px 14px;
    margin: 0 3px;
    border-radius: 4px;
    text-decoration: none;
}

.pagination li.active span {
    background-color: #15161D;
    border-color: #15161D;
    color: #FFF;
}

.pagination li.active span,
.pagination li.active span:hover {
    background-color: #15161D !important;
    border-color: #15161D !important;
    color: #FFF !important;
}

.pagination li a:hover {
    background-color: #15161D;
    border-color: #15161D;
    color: #FFF;
}

.pagination li.disabled span {
    background-color: #ccc;
    border-color: #ccc;
    color: #666;
}
</style>
@if ($paginator->hasPages())
 <ul class="pagination">
  {{-- Previous Page Link --}}
  @if ($paginator->onFirstPage())
   <li class="disabled"><span>&laquo;</span></li>
  @else
   <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
  @endif
  {{-- Pagination Elements --}}
  @foreach ($elements as $element)
   {{-- "Three Dots" Separator --}}
   @if (is_string($element))
    <li class="disabled"><span>{{ $element }}</span></li>
   @endif
   {{-- Array Of Links --}}
   @if (is_array($element))
    @foreach ($element as $page => $url)
     @if ($page == $paginator->currentPage())
      <li class="active"><span>{{ $page }}</span></li>
     @else
      <li><a href="{{ $url }}">{{ $page }}</a></li>
     @endif
    @endforeach
   @endif
  @endforeach
  {{-- Next Page Link --}}
  @if ($paginator->hasMorePages())
   <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
  @else
   <li class="disabled"><span>&raquo;</span></li>
  @endif
 </ul>
@endif
