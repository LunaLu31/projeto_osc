@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Paginação das demandas">
        @if ($paginator->onFirstPage())
            <span class="button button--secondary button--small" aria-disabled="true">Anterior</span>
        @else
            <a class="button button--secondary button--small" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
        @endif

        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="button button--secondary button--small" href="{{ $paginator->nextPageUrl() }}" rel="next">Próxima</a>
        @else
            <span class="button button--secondary button--small" aria-disabled="true">Próxima</span>
        @endif
    </nav>
@endif
