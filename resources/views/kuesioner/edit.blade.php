<input type="hidden" name="id_kuesioner" value="{{ $dataKuesioner->id_kuesioner }}">
<div class="row p-3 g-3">
    <div class="col-md-8">
        <label for="judulKuesioner" class="form-label">Judul Kuesioner</label>
        <input type="text" class="form-control" name="que_title" id="judulKuesioner" value="Partner Satisfaction Survey"
            readonly>
    </div>
    <div class="col-md-4">
        <label for="statusKuesioner" class="form-label">Status Kuesioner</label>
        <select class="form-control select2" name="status" id="statusKuesioner">
            <option value="Open" {{ $dataKuesioner->status == 'Open' ? 'selected' : '' }}>Open</option>
            <option value="Close" {{ $dataKuesioner->status == 'Close' ? 'selected' : '' }}>Close</option>
        </select>
    </div>

    <div class="col-md-6">
        <label for="templateKuesioner" class="form-label">Template Kuesioner</label>
        <select class="form-control select2" name="que_for" id="templateKuesioner">
            <option value="University-Partner" {{ $dataKuesioner->que_for == 'University-Partner' ? 'selected' : '' }}>
                University-Partner</option>
            <option value="Activity" {{ $dataKuesioner->que_for == 'Activity' ? 'selected' : '' }}>Activity
            </option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="waktuPembuatan" class="form-label">Waktu Pembuatan Kuesioner</label>
        <input type="text" class="form-control" name="que_create" id="waktuPembuatan"
            value="{{ date('Y-m-d H:i:s') }}" readonly>
    </div>
</div>
