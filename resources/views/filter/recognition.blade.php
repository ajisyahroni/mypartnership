<div class="collapse mt-3 mb-3 px-5" id="filterRecognition">
    <div class="filter-box">
        <div class="py-3">
            <h5><i class="fas fa-filter"></i> Filter Options</h5>
        </div>
        <form action="{{ route('recognition.getDataAjuan') }}" method="get" id="formFilterRecognition">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-calendar-alt"></i>
                        Tahun Mulai SK Dosen</label>
                    <select class="form-select select2" name="tahun" id="tahun">
                        {!! $filterRecognition['tahun'] !!}
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger btn-sm me-3 btn-reset-recognition"><i
                        class="bx bx-reset"></i> Reset
                    Filter</button>
                <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i> Apply Filter</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        let inputs = {
            tahun: $("#tahun"),
        };

        $(".btn-reset-recognition").click(function() {
            $.each(inputs, function(key, $el) {
                $el.val('').trigger('change');
            });
        })
    })

</script>
