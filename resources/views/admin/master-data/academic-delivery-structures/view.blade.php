<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label text-muted small">Course</label>
                <p class="mb-0">{{ $academicDeliveryStructure->course ? $academicDeliveryStructure->course->title : '-' }}</p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label text-muted small">Title</label>
                <p class="mb-0">{{ $academicDeliveryStructure->title }}</p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label text-muted small">Descriptions</label>
                @php $descriptions = $academicDeliveryStructure->descriptions ?? []; @endphp
                @if(count($descriptions) > 0)
                    <ul class="mb-0 ps-3">
                        @foreach($descriptions as $desc)
                            <li>{{ $desc }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-0 text-muted">— No descriptions</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
