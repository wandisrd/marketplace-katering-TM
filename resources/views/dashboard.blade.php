<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Marketplace Katering</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="#">Marketplace Katering</a>

    <div class="text-white">
        Welcome, {{ Auth::user()->name }}
    </div>

    <a href="/logout_user" class="btn btn-danger btn-sm">Logout</a>
</nav>

<!-- Content -->
 @if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<div class="container mt-4">

    <!-- Info User -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white">
            <strong>👤 Informasi User</strong>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Nama</small>
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                </div>

                <div class="col-md-6 mb-2">
                    <small class="text-muted">Email</small>
                    <div class="fw-bold">{{ Auth::user()->email }}</div>
                </div>

            </div>

            <hr>

            <div class="row">

                <div class="col-md-6 mb-2">
                    <small class="text-muted">Perusahaan</small>
                    <div>{{ Auth::user()->nama_perusahaan ?? '-' }}</div>
                </div>

                <div class="col-md-6 mb-2">
                    <small class="text-muted">Kontak</small>
                    <div>{{ Auth::user()->kontak ?? '-' }}</div>
                </div>

                <div class="col-12 mb-2">
                    <small class="text-muted">Alamat</small>
                    <div>{{ Auth::user()->alamat ?? '-' }}</div>
                </div>

                <div class="col-12">
                    <small class="text-muted">Deskripsi</small>
                    <div>{{ Auth::user()->deskripsi ?? '-' }}</div>
                </div>

            </div>

        </div>
    </div>

    <!-- Menu Dashboard -->
    <div class="row">

        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>🍱 Menu Katering</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_menu">
                        Kelola Menu Makanan
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>📦 Order</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop_order">
                        Daftar Pesanan Masuk
                    </button>
                </div>
            </div>
        </div>


        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5>👤 Profile</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        edit data user
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal profile-->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Data User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="/update_user">
        {{ csrf_field() }}

        <div class="modal-body">

            <div class="mb-2">
                <label>Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control"
                       value="{{ Auth::user()->nama_perusahaan }}">
            </div>

            <div class="mb-2">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ Auth::user()->alamat }}</textarea>
            </div>

            <div class="mb-2">
                <label>Kontak</label>
                <input type="number" name="kontak" class="form-control"
                       value="{{ Auth::user()->kontak }}">
            </div>

            <div class="mb-2">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control">{{ Auth::user()->deskripsi }}</textarea>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- Modal Menu-->
<div class="modal fade" id="staticBackdrop_menu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Kelola Menu Makanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
        <div class="modal-body">
            @if(session('success_menu'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success_menu') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            <form method="POST" action="/input_menu" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row">

                    <input type="hidden" id="id">

                    <div class="col-md-6 mb-2">
                        <label>Nama Menu</label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Jenis Makanan</label>
                        <select id="jenis_makanan" name="jenis_makanan" class="form-control">
                            <option value="">-- Pilih Jenis Makanan --</option>
                            <option value="healty_food">Healty Food</option>
                            <option value="junk_food">Junk Food</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Harga</label>
                        <input type="number" id="harga" name="harga" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control">
                        <img id="preview_foto" src="" width="80" class="mt-2">
                    </div>

                    <div class="col-12 mb-2">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi"  id="deskripsi" class="form-control"></textarea>
                    </div>

                </div>

                <button class="btn btn-success btn-sm" id="status_button">
                    Tambah Menu
                </button>

            </form>

            <hr>

            <button class="btn btn-primary btn-sm" id="t_button" onclick="status_add()">
                Tambah Menu
            </button><br><br>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Harga</th>
                            <th>Foto</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach(DB::table('menus')->where('id_user', Auth::user()->id)->get() as $menu)
                        <tr>
                            <td>{{ $menu->nama }}</td>
                            <td>{{ $menu->jenis_makanan }}</td>
                            <td>Rp {{ number_format($menu->harga) }}</td>
                            <td>
                                @if($menu->foto)
                                    <img src="{{ asset('uploads/'.$menu->foto) }}" width="50">
                                @endif
                            </td>
                            <td>{{ $menu->deskripsi }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-menu="{{ e(json_encode($menu)) }}"
                                    onclick="updateMenuFromData(this)">
                                    Edit
                                </button>
                                <a href="/menu/delete/{{ $menu->id }}" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>

        </div>
    </div>
  </div>
</div>

<!-- Modal order-->
<div class="modal fade" id="staticBackdrop_order" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Data Pesanan Masuk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Nama Customer</th>
                            <th>Nama Menu</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    
                        @foreach(DB::select("SELECT a.id as id_transaksi,
                                                    a.kode_transaksi as kode_transaksi, 
                                                    customer.name as nama_customer, 
                                                    c.nama as nama_menu, 
                                                    b.harga as harga_menu, 
                                                    b.jumlah as jumlah_menu, 
                                                    b.total as totall
                                                FROM transactions a
                                                LEFT JOIN detail_transactions b ON a.id = b.id_transaksi
                                                LEFT JOIN menus c ON c.id = b.id_menu
                                                LEFT JOIN users merchant ON merchant.id = c.id_user AND merchant.type = 1
                                                LEFT JOIN users customer ON customer.id = a.id_user AND customer.type = 2
                                                WHERE merchant.id = ?
                                            ", [Auth::user()->id]) as $menu)
                        <tr>
                            <td>{{ $menu->kode_transaksi }}</td>
                            <td>{{ $menu->nama_customer }}</td>
                            <td>{{ $menu->nama_menu }}</td>
                            <td>Rp {{ number_format($menu->harga_menu) }}</td>
                            <td>{{ $menu->jumlah_menu }}</td>
                            <td>Rp {{ number_format($menu->totall) }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    onclick="pesanan_terima()">
                                    Pesanan diterima
                                </button>
                                <a href="/print/invoice/{{ $menu->kode_transaksi }}" class="btn btn-success btn-sm">Cetak Invoice</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>

            </div>

        </div>
    </div>
  </div>
</div>
</body>
</html>
@if(session('open_modal'))
<script>
    window.onload = function () {
        var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop_menu'));
        myModal.show();
    }
</script>
@endif
<!-- Bootstrap JS (WAJIB untuk modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function pesanan_terima(){
        alert('belum beress maaf, iniya aja')
    }

    function updateMenuFromData(el)
    {
        let menu = JSON.parse(el.getAttribute('data-menu'));

        document.getElementById('id').value = menu.id;
        document.getElementById('nama').value = menu.nama;
        document.getElementById('jenis_makanan').value = menu.jenis_makanan;
        document.getElementById('harga').value = menu.harga;
        document.getElementById('deskripsi').value = menu.deskripsi;

        // FOTO
        let foto = menu.foto;

        if (foto) {
            document.getElementById('preview_foto').src = '/uploads/' + foto;
        } else {
            document.getElementById('preview_foto').src = '';
        }

        document.getElementById('status_button').innerText = 'Update Menu';
        document.getElementById('t_button').style.display = 'block';
    }

    function status_add(){
        document.getElementById('id').value = 0;
        document.getElementById('nama').value = '';
        document.getElementById('jenis_makanan').value = '';
        document.getElementById('harga').value = '';
        document.getElementById('deskripsi').value = '';

        // FOTO
        let foto = '';

        if (foto) {
            document.getElementById('preview_foto').src = '/uploads/' + foto;
        } else {
            document.getElementById('preview_foto').src = '';
        }
        document.getElementById('status_button').innerText = 'Tambah Menu';
        document.getElementById('t_button').style.display = 'none';
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('t_button').style.display = 'none';
    });
</script>
