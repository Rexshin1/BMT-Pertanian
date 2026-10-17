<?php

namespace Tests\Feature;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookkeepingTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_created_and_purchases_increase_stock(): void
    {
        $this->post(route('pembelian.store'), [
            'nama_barang' => 'Kopi',
            'tanggal' => '2026-10-16',
            'satuan' => 'per bungkus',
            'jumlah' => '10',
            'harga_beli' => '8250',
        ])->assertRedirect(route('pembelian.create'))
            ->assertSessionHas('success', 'Pembelian dicatat dan stok barang bertambah.');

        $produk = Produk::query()->where('nama', 'Kopi')->firstOrFail();
        $this->assertSame('10.000', $produk->fresh()->stok);
        $this->assertSame('8250.00', $produk->fresh()->harga_beli);
        $this->assertDatabaseHas('pembelians', [
            'produk_id' => $produk->id,
            'satuan' => 'per bungkus',
            'jumlah' => '10.000',
            'total_harga' => '82500.00',
        ]);

        $this->get(route('pembelian.master-data'))
            ->assertOk()
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Nama Barang')
            ->assertSee('Unit')
            ->assertSee('Harga')
            ->assertSee('Jumlah')
            ->assertSee('per bungkus')
            ->assertSee('>10</td>', false)
            ->assertDontSee('>10,000</td>', false);
    }

    public function test_sales_reduce_stock_and_keep_the_purchase_cost_snapshot_for_profit_reports(): void
    {
        $produk = Produk::query()->create([
            'nama' => 'Teh',
            'satuan' => 'kotak',
        ]);

        $this->post(route('pembelian.store'), [
            'nama_barang' => 'Teh',
            'tanggal' => '2026-10-01',
            'satuan' => 'per kotak',
            'jumlah' => '10',
            'harga_beli' => '100',
        ])->assertRedirect(route('pembelian.create'));

        $this->post(route('penjualan.store'), [
            'produk_id' => $produk->id,
            'tanggal' => '2026-10-10',
            'satuan' => 'per kotak',
            'jumlah' => '2.5',
            'harga_jual' => '150',
        ])->assertRedirect(route('penjualan.create'))
            ->assertSessionHas('success', 'Penjualan dicatat dan stok barang berkurang.');

        $this->assertSame('7.500', $produk->fresh()->stok);

        $this->post(route('pembelian.store'), [
            'nama_barang' => 'Teh',
            'tanggal' => '2026-10-12',
            'satuan' => 'per kotak',
            'jumlah' => '5',
            'harga_beli' => '120',
        ])->assertRedirect(route('pembelian.create'));

        $this->assertSame('100.00', Penjualan::query()->firstOrFail()->harga_beli_satuan);

        $this->get(route('penjualan.master-data', [
            'q' => 'Teh',
            'tanggal_mulai' => '2026-10-10',
            'tanggal_selesai' => '2026-10-10',
        ]))
            ->assertOk()
            ->assertSee('No.')
            ->assertSee('Unit')
            ->assertSee('Harga')
            ->assertSee('Jumlah')
            ->assertSee('Rp 150,00')
            ->assertSee('per kotak')
            ->assertSee('>2,5</td>', false);

        $this->get(route('laporan.laba-rugi', [
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
        ]))
            ->assertOk()
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Nama Barang')
            ->assertSee('Unit')
            ->assertSee('Harga HB')
            ->assertSee('Jumlah HB')
            ->assertSee('Harga HJ')
            ->assertSee('Jumlah HJ')
            ->assertSee('Rp 100,00')
            ->assertSee('Rp 250,00')
            ->assertSee('Rp 150,00')
            ->assertSee('Rp 375,00')
            ->assertSee('Rp 125,00');
    }

    public function test_sales_cannot_exceed_available_stock(): void
    {
        $produk = Produk::query()->create([
            'nama' => 'Susu',
            'satuan' => 'botol',
        ]);

        $this->post(route('pembelian.store'), [
            'nama_barang' => 'Susu',
            'tanggal' => '2026-10-16',
            'satuan' => 'per botol',
            'jumlah' => '1',
            'harga_beli' => '10',
        ])->assertRedirect(route('pembelian.create'));

        $this->from(route('penjualan.create'))
            ->post(route('penjualan.store'), [
                'produk_id' => $produk->id,
                'tanggal' => '2026-10-16',
                'satuan' => 'per botol',
                'jumlah' => '1.001',
                'harga_jual' => '15',
            ])
            ->assertRedirect(route('penjualan.create'))
            ->assertSessionHasErrors('jumlah');

        $this->assertSame('1.000', $produk->fresh()->stok);
        $this->assertDatabaseCount('penjuwalans', 0);
    }

    public function test_sales_require_a_selected_existing_product(): void
    {
        $this->from(route('penjualan.create'))
            ->post(route('penjualan.store'), [
                'produk_id' => 999999,
                'tanggal' => '2026-10-16',
                'satuan' => 'per pcs',
                'jumlah' => '1',
                'harga_jual' => '15',
            ])
            ->assertRedirect(route('penjualan.create'))
            ->assertSessionHasErrors('produk_id');

        $this->assertDatabaseCount('penjuwalans', 0);
    }

    public function test_sales_form_only_lists_products_with_available_stock_and_prefills_the_unit(): void
    {
        $availableProduct = Produk::query()->create([
            'nama' => 'Kopi tersedia',
            'satuan' => 'pcs',
        ]);
        $availableProduct->stok = '3';
        $availableProduct->save();

        Produk::query()->create([
            'nama' => 'Kopi habis',
            'satuan' => 'pcs',
        ]);

        $this->get(route('penjualan.create'))
            ->assertOk()
            ->assertSee('id="produk_id" name="produk_id"', false)
            ->assertSee('Kopi tersedia')
            ->assertSee('data-unit="pcs"', false)
            ->assertDontSee('Kopi habis');
    }

    public function test_dashboard_shows_live_bookkeeping_data_and_chart_series(): void
    {
        $produk = Produk::query()->create([
            'nama' => 'Barang Dashboard',
            'satuan' => 'pcs',
        ]);
        $produk->stok = '4';
        $produk->harga_beli = '500';
        $produk->harga_jual = '700';
        $produk->save();

        Pembelian::query()->create([
            'produk_id' => $produk->id,
            'tanggal' => now()->toDateString(),
            'satuan' => 'per pcs',
            'jumlah' => '2',
            'harga_beli' => '500',
            'total_harga' => '1000',
        ]);
        Penjualan::query()->create([
            'produk_id' => $produk->id,
            'tanggal' => now()->toDateString(),
            'satuan' => 'per pcs',
            'jumlah' => '1',
            'harga_beli_satuan' => '500',
            'harga_jual' => '700',
            'total_harga_beli' => '500',
            'total_harga_jual' => '700',
            'laba' => '200',
        ]);

        $response = $this->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Penjualan & Pembelian Harian', false)
            ->assertSee('Ringkasan Penjualan')
            ->assertSee('border-danger', false)
            ->assertSee('bg-danger', false)
            ->assertSee('Laba / Rugi Bulan Ini')
            ->assertSee('Barang Dashboard')
            ->assertSee('Rp 700')
            ->assertSee('Rp 200')
            ->assertSee('dashboardData')
            ->assertDontSee('John Doe')
            ->assertDontSee('Boat Headphone')
            ->assertDontSee('$36,358');

        $dashboardData = $response->viewData('dashboardData');
        $lastChartPoint = count($dashboardData['sales']) - 1;

        $this->assertSame(700.0, $dashboardData['sales'][$lastChartPoint]);
        $this->assertSame(1000.0, $dashboardData['purchases'][$lastChartPoint]);
        $this->assertSame(200.0, $dashboardData['profit'][$lastChartPoint]);
    }

    public function test_bookkeeping_pages_render_with_the_modernize_layout(): void
    {
        foreach ([
            'produk.index',
            'pembelian.index',
            'pembelian.create',
            'pembelian.master-data',
            'penjualan.index',
            'penjualan.create',
            'penjualan.master-data',
            'laporan.laba-rugi',
        ] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('assets/css/styles.min.css')
                ->assertSee('Dashboard')
                ->assertSee('MASTER DATA')
                ->assertSee('Create Penjualan')
                ->assertSee('Create Pembelian')
                ->assertSee('>Login</span>', false)
                ->assertSee('>Register</span>', false);
        }

        $this->get(route('pembelian.index'))
            ->assertSee('View Pembelian')
            ->assertSee('id="pembelian-table"', false)
            ->assertSee('id="pembelian-table-date-from"', false)
            ->assertSee('id="pembelian-table-date-to"', false)
            ->assertSee('data-date-filter-reset="pembelian-table"', false)
            ->assertSee('cdn.datatables.net/2.3.4')
            ->assertSee("search: 'Cari:'", false)
            ->assertDontSee('Dari tanggal')
            ->assertDontSee('Sampai tanggal')
            ->assertDontSee('Cari nama barang')
            ->assertSee('Belum ada data pembelian.')
            ->assertSee('ti-basket')
            ->assertDontSee('ti-shopping-bag')
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Nama Barang')
            ->assertSee('Unit')
            ->assertSee('Harga')
            ->assertSee('Jumlah')
            ->assertDontSee('+ Create Pembelian');

        $this->get(route('penjualan.index'))
            ->assertSee('View Penjualan')
            ->assertSee('id="penjualan-table"', false)
            ->assertSee('id="penjualan-table-date-from"', false)
            ->assertSee('id="penjualan-table-date-to"', false)
            ->assertSee('data-date-filter-reset="penjualan-table"', false)
            ->assertSee('cdn.datatables.net/2.3.4')
            ->assertSee("search: 'Cari:'", false)
            ->assertDontSee('Dari tanggal')
            ->assertDontSee('Sampai tanggal')
            ->assertDontSee('Cari nama barang')
            ->assertSee('Belum ada data penjualan.')
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Nama Barang')
            ->assertSee('Unit')
            ->assertSee('Harga')
            ->assertSee('Jumlah')
            ->assertDontSee('+ Create Penjualan');

        $this->get(route('laporan.laba-rugi'))
            ->assertOk()
            ->assertSee('id="laba-rugi-table"', false)
            ->assertSee('id="laba-rugi-table-date-from"', false)
            ->assertSee('id="laba-rugi-table-date-to"', false)
            ->assertSee('data-date-filter-reset="laba-rugi-table"', false)
            ->assertSee('buttons/3.2.5/js/dataTables.buttons.min.js')
            ->assertSee('buttons.html5.min.js')
            ->assertSee("extend: 'csvHtml5'", false)
            ->assertSee("extend: 'excelHtml5'", false)
            ->assertSee("extend: 'pdfHtml5'", false)
            ->assertSee('Harga HB')
            ->assertSee('Harga HJ');

        $this->get(route('pembelian.create'))
            ->assertSee('Create Pembelian');

        $this->get(route('penjualan.create'))
            ->assertSee('Create Penjualan')
            ->assertSee('id="produk_id"', false);

        $produk = Produk::query()->create([
            'nama' => 'Contoh Form',
            'satuan' => 'pcs',
        ]);
        $produk->harga_beli = '8250';
        $produk->harga_jual = '12500';
        $produk->stok = '2';
        $produk->save();

        $this->get(route('pembelian.create'))
            ->assertSee('Form Pembelian')
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Unit')
            ->assertSee('Jumlah')
            ->assertSee('Harga beli (HB)')
            ->assertSee('name="nama_barang"', false)
            ->assertDontSee('<select', false)
            ->assertSee('id="nomor" value="1" readonly', false)
            ->assertDontSee('id="nama_barang" name="nama_barang" value="" maxlength="150" readonly', false)
            ->assertDontSee('id="satuan" name="satuan" value="" placeholder="per pcs / per botol" maxlength="30" readonly', false)
            ->assertSee('name="harga_beli"', false)
            ->assertSee('Total harga pembelian (Jumlah × Harga)')
            ->assertSee('type="text" id="satuan" name="satuan"', false)
            ->assertSee('for="jumlah">Jumlah</label>', false)
            ->assertSee('id="total_jumlah" value="0,00" readonly', false)
            ->assertSee('action="http://localhost/pembelian"', false);

        $this->get(route('penjualan.create'))
            ->assertSee('Form Penjualan')
            ->assertSee('No.')
            ->assertSee('Tgl')
            ->assertSee('Unit')
            ->assertSee('Harga jual (HJ)')
            ->assertSee('name="produk_id"', false)
            ->assertSee('Contoh Form')
            ->assertSee('id="produk_id"', false)
            ->assertSee('id="nomor" value="1" readonly', false)
            ->assertDontSee('id="nama_barang" name="nama_barang"', false)
            ->assertSee('id="satuan" name="satuan"', false)
            ->assertSee('type="date" id="tanggal" name="tanggal"', false)
            ->assertSee('name="harga_jual"', false)
            ->assertSee('Total harga penjualan (Jumlah × Harga)')
            ->assertSee('id="satuan" name="satuan"', false)
            ->assertSee('type="text" id="satuan" name="satuan"', false)
            ->assertSee('id="total_jumlah" value="0,00" readonly', false)
            ->assertSee('for="harga_jual">Harga jual (HJ)</label>', false)
            ->assertSee('for="jumlah">Jumlah</label>', false)
            ->assertSee('action="http://localhost/penjualan"', false);
    }

    public function test_purchase_and_sales_tables_include_all_rows_for_datatables(): void
    {
        $produk = Produk::query()->create([
            'nama' => 'Barang tabel',
            'satuan' => 'pcs',
        ]);

        foreach (range(1, 21) as $amount) {
            Pembelian::query()->create([
                'produk_id' => $produk->id,
                'tanggal' => '2026-10-16',
                'jumlah' => '1',
                'harga_beli' => (string) $amount,
                'total_harga' => (string) $amount,
            ]);

            Penjualan::query()->create([
                'produk_id' => $produk->id,
                'tanggal' => '2026-10-16',
                'jumlah' => '1',
                'harga_beli_satuan' => '1',
                'harga_jual' => (string) ($amount + 100),
                'total_harga_beli' => '1',
                'total_harga_jual' => (string) ($amount + 100),
                'laba' => (string) ($amount + 99),
            ]);
        }

        $this->get(route('pembelian.index'))
            ->assertOk()
            ->assertSee('Rp 21,00')
            ->assertSee('data-date="2026-10-16"', false)
            ->assertDontSee('pagination');

        $this->get(route('penjualan.index'))
            ->assertOk()
            ->assertSee('Rp 121,00')
            ->assertSee('data-date="2026-10-16"', false)
            ->assertDontSee('pagination');

        $this->get(route('laporan.laba-rugi', [
            'tanggal_mulai' => '2026-10-16',
            'tanggal_selesai' => '2026-10-16',
        ]))
            ->assertOk()
            ->assertSee('id="laba-rugi-table"', false)
            ->assertSee('Rp 121,00')
            ->assertSee('data-date="2026-10-16"', false)
            ->assertDontSee('pagination');

        $this->get(route('pembelian.create'))
            ->assertOk()
            ->assertSee('id="nomor" value="22" readonly', false);

        $this->get(route('penjualan.create'))
            ->assertOk()
            ->assertSee('id="nomor" value="22" readonly', false);
    }
}
