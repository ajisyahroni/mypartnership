 <style>
        .dokumen-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }
        
        .dokumen-card {
            background: white;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        
        .dokumen-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: #0d6efd;
            transform: translateY(-2px);
        }
        
        .dokumen-info {
            flex: 1;
            min-width: 0;
        }
        
        .dokumen-nama {
            font-weight: 600;
            color: #212529;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .dokumen-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-kerjasama {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-recognition {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-hibah {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .btn-download {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .dokumen-scroll {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 8px;
        }
        
        .dokumen-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .dokumen-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .dokumen-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .dokumen-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .dokumen-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dokumen-nama {
                white-space: normal;
            }
            
            .btn-download {
                width: 100%;
                justify-content: center;
            }
            
            .dokumen-container {
                padding: 15px;
            }
        }
    </style>


 <div class="dokumen-container">
    <div class="dokumen-search-wrapper mb-3">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                   class="form-control border-start-0 ps-0" 
                   id="searchDokumen" 
                   placeholder="Cari dokumen..."
                   autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="search-result-info mt-2" id="searchResultInfo" style="display: none;">
            <small class="text-muted">
                Menampilkan <span id="resultCount">0</span> dari <span id="totalCount">{{ count($dokumenPendukung) }}</span> dokumen
            </small>
        </div>
    </div>

    <div class="dokumen-scroll">
        @forelse ($dokumenPendukung as $dokumen)
            <div class="dokumen-card" data-dokumen-name="{{ strtolower($dokumen['nama_dokumen']) }}" data-dokumen-jenis="{{ strtolower($dokumen['jenis']) }}">
                <div class="dokumen-info">
                    <div class="dokumen-nama">
                        {{ $dokumen['nama_dokumen'] }}
                    </div>
                    <span class="dokumen-badge badge-{{ $dokumen['jenis'] }}">
                        {{ $dokumen['jenis'] }}
                    </span>
                </div>
                <a class="btn btn-primary btn-download"
                   href="{{ $dokumen['file_dokumen'] ? asset('storage/' . $dokumen['file_dokumen']) : $dokumen['link_dokumen'] }}"
                   target="_blank"
                   rel="noopener noreferrer">
                    <i class="bi bi-download"></i>
                    <span>Download</span>
                </a>
            </div>
        @empty
            <div class="empty-state" id="emptyState">
                <i class="bi bi-folder-x"></i>
                <h5>Tidak Ada Dokumen</h5>
                <p>Belum ada dokumen pendukung yang tersedia.</p>
            </div>
        @endforelse
        
        <div class="empty-state" id="noResultsState" style="display: none;">
            <i class="bi bi-search"></i>
            <h5>Tidak Ditemukan</h5>
            <p>Tidak ada dokumen yang sesuai dengan pencarian "<span id="searchQuery"></span>"</p>
        </div>
    </div>
</div>

<style>
.dokumen-search-wrapper {
    padding: 0 1rem;
}

.dokumen-search-wrapper .input-group-text {
    background-color: #fff;
    border-right: none;
    color: #6c757d;
}

.dokumen-search-wrapper .form-control {
    border-left: none;
    padding-left: 0;
}

.dokumen-search-wrapper .form-control:focus {
    border-color: #ced4da;
    box-shadow: none;
}

.dokumen-search-wrapper .input-group:focus-within .input-group-text {
    border-color: #86b7fe;
}

.dokumen-search-wrapper .input-group:focus-within .form-control {
    border-color: #86b7fe;
}

#clearSearch {
    border-left: none;
    padding: 0.375rem 0.75rem;
}

.search-result-info {
    padding: 0 0.5rem;
}

.dokumen-card.highlight {
    animation: highlightFade 0.5s ease-in-out;
}

@keyframes highlightFade {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

.dokumen-card.hidden {
    display: none !important;
}

.dokumen-card {
    transition: opacity 0.2s ease-in-out;
}
</style>

<script>
(function initDokumenSearch() {

    const searchInput = document.getElementById('searchDokumen');
    if (!searchInput) {
        console.warn('searchDokumen tidak ditemukan');
        return;
    }

    const clearBtn = document.getElementById('clearSearch');
    const dokumenCards = document.querySelectorAll('.dokumen-card');
    const emptyState = document.getElementById('emptyState');
    const noResultsState = document.getElementById('noResultsState');
    const searchResultInfo = document.getElementById('searchResultInfo');
    const resultCount = document.getElementById('resultCount');
    const searchQuery = document.getElementById('searchQuery');

    const totalDokumen = dokumenCards.length;

    function searchDokumen() {
        const query = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        console.log('INPUT:', query);

        clearBtn.style.display = query ? 'block' : 'none';

        if (query === '') {
            dokumenCards.forEach(card => card.classList.remove('hidden'));
            searchResultInfo.style.display = 'none';
            noResultsState.style.display = 'none';
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        dokumenCards.forEach(card => {
            const nama = card.dataset.dokumenName || '';
            const jenis = card.dataset.dokumenJenis || '';

            if (nama.includes(query) || jenis.includes(query)) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        resultCount.textContent = visibleCount;
        searchResultInfo.style.display = 'block';

        if (visibleCount === 0) {
            noResultsState.style.display = 'block';
            searchQuery.textContent = query;
        } else {
            noResultsState.style.display = 'none';
        }
    }

    function clearSearch() {
        searchInput.value = '';
        searchInput.focus();
        searchDokumen();
    }

    searchInput.addEventListener('input', searchDokumen);
    searchInput.addEventListener('keyup', e => {
        console.log('KEYUP');
        if (e.key === 'Escape') clearSearch();
    });

    clearBtn.addEventListener('click', clearSearch);

})();
</script>
