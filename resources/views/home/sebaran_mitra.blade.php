{{-- Sebaran Mitra --}}
<div id="mitra" class="content-section p-3">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-12 col-md-6 mb-2">
                <div class="title-bar"></div>
                <h3 class="title-dashboard">Sebaran Mitra</h3>
            </div>

            <div class="col-12 col-md-4 mb-2">
                <label for="filterSebaranMitra" class="form-label fw-bold"><b>Filter Lembaga:</b></label>
                <div class="input-group">
                    <select name="filterSebaranMitra" id="filterSebaranMitra" class="form-control select2">
                        <option value="Universitas">Universitas</option>
                        @foreach ($RefLembagaUMS as $lmbg)
                            <option value="{{ $lmbg->nama_lmbg }}" {{ @$q == $lmbg->nama_lmbg ? 'selected' : '' }}
                                data-place_state="{{ $lmbg->id_lmbg }}">
                                {{ $lmbg->nama_lmbg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-2 mb-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-success w-100" onclick="downloadSebaranMitraAktif()">
                    <i class="fa fa-download"></i> Unduh Data
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Skor Prodi 1 Tahun Terakhir -->
            <div class="col-md-6 mb-3">
                <div class="ranking-card p-3 text-center">
                    <span class="title-dashboard">Dalam Negeri</span>
                    <div class="point-box platinum" onclick="detailKerma('Dalam Negeri','Kerja Sama Dalam Negeri')">
                        {{ round(@$KermaDN, 2) }}
                    </div>
                </div>
            </div>

            <!-- Skor Rata Rata 1 Tahun Terakhir -->
            <div class="col-md-6 mb-3">
                <div class="ranking-card p-3 text-center">
                    <span class="title-dashboard">Luar Negeri</span>
                    <div class="point-box platinum " onclick="detailKerma('Luar Negeri','Kerja Sama Luar Negeri')">
                        {{ round(@$KermaLN, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div id="mapid" style="width: 100%; height: 400px;background-color: white;"></div>
        <div class="no-data-sebaran-mitra2 d-none text-center text-muted"
            style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.9); z-index: 5;">
            Tidak ada data untuk sebaran mitra.
        </div>
    </div>
</div>


<style>
    #mapid {
        position: relative;
        /* penting supaya control Leaflet nempel ke area map */
        z-index: 0;
        /* biar dasar */
    }

    .leaflet-control {
        z-index: 1000 !important;
        /* biar pasti di atas layer peta */
    }

    /* Tooltip custom elegan */
    .custom-tooltip {
        background: #fff !important;
        color: #333 !important;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        border: 1px solid #ddd;
    }

    .custom-tooltip::before {
        border-top-color: #fff !important;
    }
</style>
<!-- Tambahkan Leaflet -->
<script>
    function downloadSebaranMitraAktif(){
        const selectedOptionSebaran = $("#filterSebaranMitra").find("option:selected");
        const selectedFilter = selectedOptionSebaran.val();
        const placeState = selectedOptionSebaran.data("place_state");

        const urlDownload = 'home/download-excel-sebaran-mitra-aktif';

        let params = [];

        if (selectedFilter) {
            params.push(`q=${encodeURIComponent(selectedFilter)}`);
        }
        if (placeState) {
            params.push(`ps=${encodeURIComponent(placeState)}`);
        }

        const targetUrl = params.length ? `${urlDownload}?${params.join("&")}` : urlDownload;
        
        window.open(targetUrl, "_blank");

    }
    
</script>

<script>
    // Data dari backend Laravel
    const dataNegara2 = @json($dataNegara2);

    // Jika data kosong, tampilkan pesan "Tidak ada data"
    if (!dataNegara2 || dataNegara2.length === 0) {
        document.getElementById('mapid').style.display = "none";
        document.querySelector('.no-data-sebaran-mitra2').classList.remove('d-none');
    } else {
        document.getElementById('mapid').style.display = "block";
        document.querySelector('.no-data-sebaran-mitra2').classList.add('d-none');

        const map = L.map('mapid', {
            minZoom: 1,
            zoomControl: true,
            attributionControl: false
        });

        // Background putih (bukan tile map standar)
        L.tileLayer('', {
            attribution: ''
        }).addTo(map);

        // Mapping data negara -> jumlah
        const negaraDataMap = {};
        let minJumlah = Infinity;
        let maxJumlah = -Infinity;

        dataNegara2.forEach(item => {
            let nama = item.nama_negara.toLowerCase();

            // Normalisasi nama negara
            if (nama === "korea republic of") nama = "south korea";
            if (nama === "hong kong") nama = "hong kong"; // tetap sama
            // Tambah mapping lain kalau ada beda penulisan

            if (!negaraDataMap[nama]) negaraDataMap[nama] = 0;
            negaraDataMap[nama] += item.jumlah;

            if (item.jumlah < minJumlah) minJumlah = item.jumlah;
            if (item.jumlah > maxJumlah) maxJumlah = item.jumlah;
        });

        if (minJumlah === Infinity) minJumlah = 0;
        if (maxJumlah === -Infinity) maxJumlah = 0;

        fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json")
            .then(res => res.json())
            .then(geojson => {

                const hongKongFeature = {
                    "type": "Feature",
                    "properties": {
                        "name": "Hong Kong"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [113.837, 22.153],
                                [114.432, 22.153],
                                [114.432, 22.561],
                                [113.837, 22.561],
                                [113.837, 22.153]
                            ]
                        ]
                    }
                };
                geojson.features.push(hongKongFeature);

                // Hitung kuartil untuk pembagian 5 gradasi
                const values = Object.values(negaraDataMap).sort((a, b) => a - b);
                const q1 = values[Math.floor(values.length * 0.25)] || 1;
                const q2 = values[Math.floor(values.length * 0.5)] || 1;
                const q3 = values[Math.floor(values.length * 0.75)] || 1;

                function getColor(jumlah) {
                    if (jumlah === 0) return "rgb(204,204,204)"; // abu-abu
                    else if (jumlah <= q1) return "#c6dbef"; // biru muda
                    else if (jumlah <= q2) return "#6baed6"; // biru sedang
                    else if (jumlah <= q3) return "#2171b5"; // biru tua
                    else return "#08306b"; // biru sangat gelap
                }

                const layerGeo = L.geoJson(geojson, {
                    filter: function(feature) {
                        const name = (feature.properties.name || "").toLowerCase();
                        return !name.includes("antarctica");
                    },
                    style: function(feature) {
                        const nama = feature.properties.name.toLowerCase();
                        const jumlah = negaraDataMap[nama] || 0;

                        return {
                            fillColor: getColor(jumlah),
                            weight: 1,
                            opacity: 1,
                            color: (jumlah === 0 ? "rgb(184,184,184)" : "rgb(4,14,74)"), // stroke tetap
                            fillOpacity: 1
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const nama = feature.properties.name.toLowerCase();
                        const jumlah = negaraDataMap[nama] || 0;

                        layer.bindTooltip(
                            `<div>
                        <strong>${feature.properties.name}</strong><br>
                        <span style="color:#555;">${jumlah} Mitra</span>
                    </div>`, {
                                permanent: false,
                                direction: "top",
                                offset: [0, -5],
                                className: "custom-tooltip"
                            }
                        );

                        layer.on('mouseover', function() {
                            layer.setStyle({
                                weight: 2,
                                color: (jumlah === 0 ? "rgb(184,184,184)" :
                                    "rgb(4,14,74)"), // tetap sesuai isi
                                fillOpacity: 1
                            });
                            layer.bringToFront();
                        });

                        layer.on('mouseout', function() {
                            layer.setStyle(layer.options.style(feature));
                        });
                    }
                }).addTo(map);

                map.fitBounds(layerGeo.getBounds());

                // Legend baru
                const legend = L.control({
                    position: 'bottomright'
                });
                legend.onAdd = function() {
                    const div = L.DomUtil.create('div', 'info legend');
                    div.innerHTML = `
                <div style="
                    background: #fff;
                    padding: 12px 15px;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    font-family: Arial, sans-serif;
                    font-size: 13px;
                    line-height: 1.5;
                    color: #333;
                ">
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 6px;">
                        Jumlah Mitra
                    </div>
                    <div><span style="background:#ccc;width:18px;height:18px;display:inline-block;margin-right:6px;border:1px solid rgb(184,184,184);"></span> 0</div>
                    <div><span style="background:#c6dbef;width:18px;height:18px;display:inline-block;margin-right:6px;border:1px solid rgb(4,14,74);"></span> 1 – ${q1}</div>
                    <div><span style="background:#6baed6;width:18px;height:18px;display:inline-block;margin-right:6px;border:1px solid rgb(4,14,74);"></span> ${q1+1} – ${q2}</div>
                    <div><span style="background:#2171b5;width:18px;height:18px;display:inline-block;margin-right:6px;border:1px solid rgb(4,14,74);"></span> ${q2+1} – ${q3}</div>
                    <div><span style="background:#08306b;width:18px;height:18px;display:inline-block;margin-right:6px;border:1px solid rgb(4,14,74);"></span> > ${q3}</div>
                </div>
            `;
                    return div;
                };
                legend.addTo(map);
            });


    }
</script>
