<div class="row align-items-end g-3 mb-3">
  <div class="col-sm-4 col-md-3">
    <label class="form-label" for="{{ $tableId }}-date-from">Tanggal dari</label>
    <input class="form-control" type="date" id="{{ $tableId }}-date-from">
  </div>
  <div class="col-sm-4 col-md-3">
    <label class="form-label" for="{{ $tableId }}-date-to">Tanggal sampai</label>
    <input class="form-control" type="date" id="{{ $tableId }}-date-to">
  </div>
  <div class="col-sm-4 col-md-3">
    <button class="btn btn-outline-secondary" type="button" data-date-filter-reset="{{ $tableId }}">Reset tanggal</button>
  </div>
</div>
