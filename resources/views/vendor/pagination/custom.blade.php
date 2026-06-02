@if ($paginator->hasPages())
    <nav class="custom-pagination-container" aria-label="Pagination"
        style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 15px; padding: 10px 0;">
        <!-- Left Side: Results Info -->
        <div class="pagination-info" style="font-size: 13px; color: #64748b; font-weight: 500;">
            Affichage de <span style="font-weight: 700; color: #1e293b;">{{ $paginator->firstItem() }}</span>
            à <span style="font-weight: 700; color: #1e293b;">{{ $paginator->lastItem() }}</span>
            sur <span style="font-weight: 700; color: #1e293b;">{{ $paginator->total() }}</span> résultats
        </div>

        <!-- Right Side: Navigation Buttons -->
        <div class="pagination-buttons-wrapper" style="display: flex; align-items: center; gap: 5px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-nav-btn disabled" aria-disabled="true"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; cursor: not-allowed; transition: all 0.2s;">
                    <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-nav-btn" rel="prev"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.03);"
                    onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b'; this.style.background='#f8fafc';"
                    onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.background='white';">
                    <i class="fa-solid fa-chevron-left" style="font-size: 11px;"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="page-dots"
                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-num active" aria-current="page"
                                style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #1e293b; color: white; font-weight: 700; font-size: 13px; border: 1px solid #1e293b; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-num"
                                style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.03);"
                                onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b'; this.style.background='#f8fafc';"
                                onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.background='white';">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-nav-btn" rel="next"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: white; color: #475569; text-decoration: none; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.03);"
                    onmouseover="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b'; this.style.background='#f8fafc';"
                    onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569'; this.style.background='white';">
                    <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                </a>
            @else
                <span class="page-nav-btn disabled" aria-disabled="true"
                    style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; cursor: not-allowed; transition: all 0.2s;">
                    <i class="fa-solid fa-chevron-right" style="font-size: 11px;"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
