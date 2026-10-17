@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.5/css/buttons.dataTables.min.css">
@endpush

@push('scripts')
  <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/vfs_fonts.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.2.5/js/buttons.html5.min.js"></script>
  <script>
    const dateFrom = document.getElementById('{{ $tableId }}-date-from');
    const dateTo = document.getElementById('{{ $tableId }}-date-to');

    DataTable.ext.search.push((settings, _data, dataIndex) => {
      if (settings.nTable.id !== '{{ $tableId }}') {
        return true;
      }

      const transactionDate = settings.aoData[dataIndex].nTr.dataset.date || '';

      return (!dateFrom.value || transactionDate >= dateFrom.value)
        && (!dateTo.value || transactionDate <= dateTo.value);
    });

    const table = new DataTable('#{{ $tableId }}', {
      order: [],
      pageLength: 20,
      lengthMenu: [10, 20, 50, 100],
      columnDefs: [{ targets: 0, orderable: false, searchable: false }],
      layout: {
        topStart: {
          buttons: [
            { extend: 'csvHtml5', text: 'CSV', title: @json($title), exportOptions: { columns: ':visible' } },
            { extend: 'excelHtml5', text: 'Excel', title: @json($title), exportOptions: { columns: ':visible' } },
            { extend: 'pdfHtml5', text: 'PDF', title: @json($title), orientation: 'landscape', pageSize: 'A4', exportOptions: { columns: ':visible' } }
          ]
        },
        topEnd: 'search',
        bottomStart: 'info',
        bottomEnd: 'paging'
      },
      language: {
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
        infoEmpty: 'Tidak ada data untuk ditampilkan',
        zeroRecords: 'Data tidak ditemukan',
        emptyTable: @json($emptyMessage),
        paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' },
        buttons: { csv: 'CSV', excel: 'Excel', pdf: 'PDF' }
      },
      drawCallback: function () {
        const api = this.api();
        const start = api.page.info().start;
        api.column(0, { page: 'current' }).nodes().each((cell, index) => {
          cell.textContent = start + index + 1;
        });
      }
    });

    dateFrom.addEventListener('change', () => table.draw());
    dateTo.addEventListener('change', () => table.draw());
    document.querySelector('[data-date-filter-reset="{{ $tableId }}"]').addEventListener('click', () => {
      dateFrom.value = '';
      dateTo.value = '';
      table.draw();
    });
  </script>
@endpush
