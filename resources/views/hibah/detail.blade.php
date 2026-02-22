<link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<style>
    .table {
        font-size: 12px;
    }

    .table td,
    .table th {
        padding: 4px 6px !important;
        line-height: 1.3;
        vertical-align: middle;
    }

    .table td img {
        max-width: 300px;
        height: auto;
        display: block;
        margin-top: 5px;
    }

    html {
        scroll-behavior: smooth;
    }
</style>

@php
    function encodedFileUrl($filePath)
    {
        return $filePath ? asset('storage/' . ltrim($filePath, '/')) : '#';
    }
@endphp

<ul class="nav nav-tabs" id="myTab" role="tablist">
    @php
        $tabs = [
            ['id' => 'pengajuan', 'label' => 'Detail Pengajuan Hibah'],
            ['id' => 'laporan', 'label' => 'Detail Laporan'],
        ];
    @endphp
    @foreach ($tabs as $index => $tab)
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($index === 0) active @endif" id="{{ $tab['id'] }}-tab"
                data-bs-toggle="tab" data-bs-target="#{{ $tab['id'] }}" type="button" role="tab"
                aria-controls="{{ $tab['id'] }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                {{ $tab['label'] }}
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content mt-3" id="myTabContent">
    <div class="tab-pane fade show active" id="pengajuan" role="tabpanel" aria-labelledby="pengajuan-tab">
        @include('hibah.detail_hibah_pengajuan', ['dataHibah' => $dataHibah])
    </div>
    <div class="tab-pane fade" id="laporan" role="tabpanel" aria-labelledby="laporan-tab">
        @include('hibah.detail_hibah_laporan', ['dataLaporanHibah' => $dataLaporanHibah])
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".scroll-to").forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                const target = document.getElementById(this.getAttribute("href").substring(1));
                target?.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            });
        });
    });
</script>
