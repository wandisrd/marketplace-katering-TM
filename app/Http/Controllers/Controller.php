<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade as PDF;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function index(Request $request){
        $query = DB::table('menus')
            ->join('users', 'menus.id_user', '=', 'users.id')
            ->select('menus.*', 'users.nama_perusahaan as vendor_name', 'users.alamat');
        
        if ($request->nama && $request->alamat) {
            $query->where('menus.nama', 'like', '%' . $request->nama . '%')
                    ->where('users.alamat', 'like', '%' . $request->alamat . '%')
                    ->orderBy('menus.nama', 'asc');
        }

        if ($request->nama) {
            $query->where('menus.nama', 'like', '%' . $request->nama . '%')
                    ->orderBy('menus.nama', 'asc');
        }

        if ($request->alamat) {
            $query->where('users.alamat', 'like', '%' . $request->alamat . '%')
                ->orderBy('menus.nama', 'asc');
        }

        $menus = $query->get();

        $cart = [];
        $kode_transaksi = null;

        if (Auth::check()) {
            $transaction = DB::table('transactions')
                ->where('id_user', Auth::id())
                ->where('status', 'draft') 
                ->orderBy('id', 'desc')
                ->first();
                
            if (!$transaction) {

                $kode_transaksi = $this->generateKodeTransaksi();

                DB::table('transactions')->insert([
                    'kode_transaksi' => $kode_transaksi,
                    'id_user' => Auth::id(),
                    'tanggal_order' => date('Y-m-d H:i:s'),
                    'status' => 'draft',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // ambil ulang
                $transaction = DB::table('transactions')
                    ->where('id_user', Auth::id())
                    ->where('kode_transaksi', $kode_transaksi)
                    ->first();

            } else {
                $kode_transaksi = $transaction->kode_transaksi;
            }

            $cart = DB::table('detail_transactions')
                ->where('id_transaksi', $transaction->id)
                ->get();
        }

        return view('welcome', compact('menus', 'cart', 'kode_transaksi'));
    }

    public function register_user(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'type' => 1,
        ]);

        return redirect('/')->with('success', 'Register berhasil, Silahkan Login');
    }

    public function register_user_customer(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'kontak' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'kontak' => $request->kontak,
            'password' => Hash::make($request->password),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'type' => 2,
        ]);

        return redirect('/')->with('success', 'Register berhasil, Silahkan Login');
    }

    public function login_user(Request $request){
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->type == 1) {
                return redirect('/dashboard');
            }
            return redirect('/');
        }
        return back()->with('error', 'Email / password salah');
    }

    public function dashboard(){
        $user = Auth::user();

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_perusahaan' => $user->nama_perusahaan,
            'user_alamat' => $user->alamat,
            'user_kontak' => $user->kontak,
            'user_deskripsi' => $user->deskripsi,
        ]);

        return view('dashboard');
    }

    public function logout_user(){
        Auth::logout();

        return redirect('/')->with('success', 'Anda berhasil logout');
    }

    public function update_user(Request $request){
        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update([
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
                'deskripsi' => $request->deskripsi,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return redirect('/dashboard')->with('success', 'Data berhasil diupdate');
    }

    public function input_menu(Request $request){
        $fileName = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
        }

        if ($request->id == 0) {
            DB::table('menus')->insert([
                'id_user' => Auth::user()->id,
                'nama' => $request->nama,
                'jenis_makanan' => $request->jenis_makanan,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'foto' => $fileName,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()
                ->with('success_menu', 'Menu berhasil ditambahkan!')
                ->with('open_modal', true);
        } else {
            DB::table('menus')
                ->where('id', $request->id)
                ->update([
                    'id_user' => Auth::user()->id,
                    'nama' => $request->nama,
                    'jenis_makanan' => $request->jenis_makanan,
                    'harga' => $request->harga,
                    'deskripsi' => $request->deskripsi,
                    'foto' => $fileName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            return redirect()->back()
                ->with('success_menu', 'Menu berhasil diupdate!')
                ->with('open_modal', true);
        }        
    }

    public function delete($id)
    {
        DB::table('menus')->where('id', $id)->delete();
        return redirect()->back()
            ->with('success_menu', 'Menu berhasil dihapus!')
            ->with('open_modal', true);
    }

    public function generateKodeTransaksi()
    {
        $tanggal = date('Ymd');

        $last = DB::table('transactions')
            ->whereBetween('created_at', [
                date('Y-m-d') . ' 00:00:00',
                date('Y-m-d') . ' 23:59:59'
            ])
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->kode_transaksi, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'TRX-' . $tanggal . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function tambah_pesanan(Request $request){
        if (!Auth::check()) {
            return redirect('/login_customer');
        }

        $jumlah = $request->jumlah ?? 1;

        $transaction = DB::table('transactions')
            ->where('id_user', Auth::id())
            ->where('status', 'draft')
            ->where('kode_transaksi', $request->kode_transaksi)
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan');
        }

        $menu = DB::table('menus')->where('id', $request->id_menu)->first();

        if (!$menu) {
            return back()->with('error', 'Menu tidak ditemukan');
        }

        $existing = DB::table('detail_transactions')
            ->where('id_transaksi', $transaction->id)
            ->where('id_menu', $menu->id)
            ->first();

        if ($existing) {
            $newQty = $existing->jumlah + $jumlah;

            DB::table('detail_transactions')
                ->where('id', $existing->id)
                ->update([
                    'jumlah' => $newQty,
                    'total'  => $newQty * $menu->harga,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

        } else {

            DB::table('detail_transactions')->insert([
                'id_transaksi' => $transaction->id,
                'id_menu'      => $menu->id,
                'deskripsi'    => $menu->deskripsi,
                'foto'         => $menu->foto,
                'harga'        => $menu->harga,
                'jumlah'       => $jumlah,
                'total'        => $menu->harga * $jumlah,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        return back()->with('success', 'Pesanan ditambahkan ke keranjang');

    }

    public function delete_cart($id)
    {
        DB::table('detail_transactions')
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Item dihapus dari keranjang');
    }

    public function print_invoice(Request $request){
        DB::table('transactions')
        ->where('kode_transaksi', $request->kode_transaksi)
        ->update([
            'status' => 'Prosses',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $transaction = DB::table('transactions')
            ->where('kode_transaksi', $request->kode_transaksi)
            ->first();

        $details = DB::table('detail_transactions')
            ->where('id_transaksi', $transaction->id)
            ->get();
        
        $grandTotal = 0;

        foreach ($details as $item) {
            $grandTotal += $item->total;
        }

        $title = 'Invoice Test';
        // $tanggal = date('Y-m-d H:i:s');

        $html = '
            <html>
            <head>
                <title>Invoice '.$transaction->kode_transaksi.'</title>
                <style>
                    body {
                        font-family: Arial;
                        font-size: 12px;
                    }

                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    .info {
                        margin-bottom: 15px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    table, th, td {
                        border: 1px solid #000;
                    }

                    th, td {
                        padding: 8px;
                        text-align: left;
                    }

                    .total {
                        text-align: right;
                        margin-top: 10px;
                        font-weight: bold;
                        font-size: 14px;
                    }

                    .status {
                        padding: 5px;
                        background: #f0f0f0;
                        display: inline-block;
                    }
                </style>
            </head>
            <body>

            <div class="header">
                <h2>INVOICE TRANSAKSI</h2>
            </div>

            <div class="info">
                <p><b>Kode Transaksi:</b> '.$transaction->kode_transaksi.'</p>
                <p><b>Tanggal Order:</b> '.$transaction->tanggal_order.'</p>
                <p><b>Status:</b> <span class="status">'.$transaction->status.'</span></p>
            </div>

            <table>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            ';

            foreach ($details as $item) {
                $html .= '
                <tr>
                    <td>'.$item->deskripsi.'</td>
                    <td>Rp '.number_format($item->harga).'</td>
                    <td>'.$item->jumlah.'</td>
                    <td>Rp '.number_format($item->total).'</td>
                </tr>';
            }

            $html .= '
            </table>

            <div class="total">
                GRAND TOTAL: Rp '.number_format($grandTotal).'
            </div>

            <br>
            <p style="text-align:center;">Terima kasih telah berbelanja 🙏</p>

            </body>
            </html>';

        $pdf = PDF::loadHTML($html);
        return $pdf->stream($title.'.pdf');
    }

    public function print_invoice_merchant($kode_transaksi){

        $transaction = DB::table('transactions')
            ->where('kode_transaksi', $kode_transaksi)
            ->first();

        $details = DB::select("
                            SELECT 
                                a.id as id_transaksi,
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
                            AND a.kode_transaksi = ?
                        ", [
                            Auth::user()->id,
                            $kode_transaksi
                        ]);
        
        $grandTotal = 0;

        foreach ($details as $item) {
            $grandTotal += $item->totall;
        }

        $title = 'Invoice Test';
        // $tanggal = date('Y-m-d H:i:s');

        $html = '
            <html>
            <head>
                <title>Invoice '.$transaction->kode_transaksi.'</title>
                <style>
                    body {
                        font-family: Arial;
                        font-size: 12px;
                    }

                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    .info {
                        margin-bottom: 15px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    table, th, td {
                        border: 1px solid #000;
                    }

                    th, td {
                        padding: 8px;
                        text-align: left;
                    }

                    .total {
                        text-align: right;
                        margin-top: 10px;
                        font-weight: bold;
                        font-size: 14px;
                    }

                    .status {
                        padding: 5px;
                        background: #f0f0f0;
                        display: inline-block;
                    }
                </style>
            </head>
            <body>

            <div class="header">
                <h2>INVOICE TRANSAKSI</h2>
            </div>

            <div class="info">
                <p><b>Kode Transaksi:</b> '.$transaction->kode_transaksi.'</p>
                <p><b>Tanggal Order:</b> '.$transaction->tanggal_order.'</p>
                <p><b>Status:</b> <span class="status">'.$transaction->status.'</span></p>
            </div>

            <table>
                <tr>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            ';

            foreach ($details as $item) {
                $html .= '
                <tr>
                    <td>Rp '.number_format($item->harga_menu).'</td>
                    <td>'.$item->jumlah_menu.'</td>
                    <td>Rp '.number_format($item->totall).'</td>
                </tr>';
            }

            $html .= '
            </table>

            <div class="total">
                GRAND TOTAL: Rp '.number_format($grandTotal).'
            </div>

            <br>
            <p style="text-align:center;">Terima kasih telah berbelanja 🙏</p>

            </body>
            </html>';

        $pdf = PDF::loadHTML($html);
        return $pdf->stream($title.'.pdf');
    }
}
