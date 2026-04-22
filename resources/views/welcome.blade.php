<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyFood</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3 fixed-top">

    <a class="navbar-brand" href="/">
        🍱 MyFood
    </a>

    <div class="ms-auto d-flex gap-2">
        @if(Auth::check() && Auth::user()->type == 2)
            <a href="/cart" class="btn btn-warning btn-sm">🛒 Riwayat Belanja</a>
            <a href="/logout_user" class="btn btn-danger btn-sm">Logout</a>
        @else
            <a href="/login_merchant" class="btn btn-outline-light btn-sm">👨‍🍳 Login Merchant</a>
        @endif

    </div>

</nav>
<br>
<div class="container mt-5">
    <div class="row">
            <div class="col-md-8">
                <!-- HEADER -->
            <div class="mb-4">
                <h3>Temukan Menu Favorit Anda</h3>
                <p class="text-muted">Cari makanan dari berbagai vendor di seluruh kota</p>
            </div>
        
            <!-- FILTER -->
            <form method="GET" action="/" class="row mb-4">
        
                <div class="col-md-4 mb-2">
                    <input type="text" name="nama" class="form-control"
                        placeholder="🔎 Cari makanan apa ?"
                        value="{{ request('nama') }}">
                </div>
        
                <div class="col-md-4 mb-2">
                    <input type="text" name="alamat" class="form-control"
                        placeholder="📍 Cari yang lokasi dimana ?"
                        value="{{ request('alamat') }}">
                </div>
        
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>
                <div class="col-md-2 mb-2">
                    <a href="/" class="btn btn-outline-secondary w-100" onclick="document.querySelectorAll('input').forEach(i => i.value='')">
                        Reset
                    </a>
                </div>
        
            </form>
        
            <!-- MENU LIST -->
            <div class="row">
        
                @forelse($menus as $menu)
                <div class="col-md-4 mb-4">
        
                    <div class="card shadow-sm h-100">
        
                        <!-- IMAGE -->
                        @if($menu->foto)
                            <img src="{{ asset('uploads/'.$menu->foto) }}"
                                class="card-img-top"
                                style="height:250px; object-fit:contain; background:#f8f9fa;">
                        @else
                            <div class="bg-secondary text-white text-center py-5">
                                No Image
                            </div>
                        @endif
        
                            <!-- BODY -->
                            <div class="card-body">
                                <h5 class="card-title">{{ $menu->nama }}</h5>
                                <p class="text-muted mb-1">{{ $menu->jenis_makanan }}</p>
                                <p class="small">{{ str_limit($menu->deskripsi, 80) }}</p>
                                <h6 class="text-success">Rp {{ number_format($menu->harga) }}</h6>
        
                                <hr>
        
                                <small class="text-muted">
                                    🏪 {{ $menu->vendor_name ?? '-' }} <br>
                                    📍 {{ $menu->alamat ?? '-' }}
                                </small>
        
                            </div>
        
                            <!-- ACTION -->
                            <div class="card-footer bg-white border-0">
            
                                @if(Auth::check() && Auth::user()->type == 2)
            
                                    <form method="POST" action="/tambah_pesanan">
                                        {{ csrf_field() }}
                                        
                                        <div class="mt-2 d-flex align-items-center gap-2">
                
                                            <button type="button" class="btn btn-danger btn-sm" data-id="{{ $menu->id }}" onclick="kurangQty(this)">-</button>
                    
                                            <input type="text"
                                                id="qty_{{ $menu->id }}"
                                                value="1"
                                                class="form-control text-center"
                                                style="width:60px;"
                                                name="jumlah"
                                                readonly>
                    
                                            <button type="button" class="btn btn-success btn-sm" data-id="{{ $menu->id }}" onclick="tambahQty(this)">+</button>
                
                                        </div>
                                        <input type="hidden" name="kode_transaksi" value="{{ $kode_transaksi }}">
                                        <input type="hidden" name="id_menu" value="{{ $menu->id }}">
            
                                        <button class="btn btn-success btn-sm w-100">
                                            🛒 Pesan
                                        </button>
                                    </form>
            
                                @else
            
                                    <a href="/login_customer" class="btn btn-warning btn-sm w-100">
                                        🔐 Pesan Sekarang
                                    </a>
            
                                @endif
            
                            </div>
        
                    </div>
        
                </div>
                @empty
        
                <!-- EMPTY STATE -->
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada menu ditemukan
                    </div>
                </div>
        
                @endforelse
        
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    🛒 Keranjang
                </div>

                <div class="card-body">

                    @if(count($cart) > 0)

                        @php $grandTotal = 0; @endphp

                        @foreach($cart as $item)
                            @php $grandTotal += $item->total; @endphp

                            <div class="d-flex mb-3 border-bottom pb-2">

                                <img src="{{ asset('uploads/'.$item->foto) }}" 
                                    width="60" height="60" 
                                    class="rounded me-2">

                                <div class="flex-grow-1">

                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>
                                            <div class="fw-bold">
                                                {{ $item->deskripsi }}
                                            </div>

                                            <small>
                                                Rp {{ number_format($item->harga) }} x {{ $item->jumlah }}
                                            </small>
                                        </div>

                                        <!-- 🗑️ BUTTON HAPUS -->
                                        <form method="POST" action="/cart/delete/{{ $item->id }}">
                                            {{ csrf_field() }}

                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus item ini?')">
                                                🗑️
                                            </button>
                                        </form>

                                    </div>

                                    <div class="text-end fw-bold text-success mt-1">
                                        Rp {{ number_format($item->total) }}
                                    </div>

                                </div>

                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span class="text-success">
                                Rp {{ number_format($grandTotal) }}
                            </span>
                        </div>

                        <form method="POST" action="/print_invoice" class="mt-3" target="_blank">
                            {{ csrf_field() }}
                            <input type="hidden" name="kode_transaksi" value="{{ $kode_transaksi }}">
                            <button target="_blank" class="btn btn-success w-100">
                                Checkout
                            </button>
                        </form>

                    @else

                        <div class="text-center text-muted">
                            Keranjang masih kosong
                        </div>

                    @endif

                </div>
            </div>
        </div>
    </div>


</div>

</body>
</html>

<script>
    function kurangQty(el) {
        let id = el.getAttribute('data-id');
        let qty = document.getElementById('qty_' + id);

        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    }

    function tambahQty(el) {
        let id = el.getAttribute('data-id');
        let qty = document.getElementById('qty_' + id);

        qty.value = parseInt(qty.value) + 1;
    }
</script>