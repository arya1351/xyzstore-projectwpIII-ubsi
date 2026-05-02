<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{
    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending', 'paid')->first();
        if ($order) {
            $order->load('orderItems.produk');
        }
        return view('v_order.cart', compact('order'));
    }
    public function addToCart($id)
    {
        $customer = Customer::firstOrCreate(['user_id' => Auth::id()]);
        $customer = Customer::where('user_id', Auth::id())->first();
        $produk = Produk::findOrFail($id);

        $order = Order::firstOrCreate(['customer_id' => $customer->id, 'status' => 'pending'], ['total_harga' => 0]);
        $orderItem = OrderItem::firstOrCreate(['order_id' => $order->id, 'produk_id' => $produk->id], ['quantity' => 1, 'harga' => $produk->harga]);
        if (!$orderItem->wasRecentlyCreated) {
            $orderItem->quantity++;
            $orderItem->save();
        }
        $order->total_harga += $produk->harga;
        $order->save();
        return redirect()->route('order.cart')->with(
            'success',
            'Produk berhasil
ditambahkan ke keranjang',
        );
    }
    public function updateCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->with('orderItems.produk')->first();

        if ($order) {
            $orderItem = $order->orderItems()->where('id', $id)->first();

            if ($orderItem) {
                $quantity = $request->input('quantity');

                if ($quantity > $orderItem->produk->stok) {
                    return redirect()->route('order.cart')->with('error', 'Jumlah produk melebihi stok yang tersedia');
                }

                // update qty
                $orderItem->quantity = $quantity;
                $orderItem->save();

                // 🔥 HITUNG ULANG TOTAL SEMUA ITEM
                $total = 0;

                foreach ($order->orderItems as $item) {
                    $total += $item->harga * $item->quantity;
                }

                // tambahin ongkir kalau ada
                $total += $order->biaya_ongkir ?? 0;

                $order->total_harga = $total;
                $order->save();
            }
        }

        return redirect()->route('order.cart')->with('success', 'Jumlah produk berhasil diperbarui');
    }

    public function checkout()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        //kurangi stok
        if ($order) {
            foreach ($order->orderItems as $item) {
                $produk = $item->produk;
                if ($produk->stok >= $item->quantity) {
                    $produk->stok -= $item->quantity;
                    $produk->save();
                } else {
                    return redirect()
                        ->route('order.cart')
                        ->with('error', 'Stok produk ' . $produk->nama_produk . ' tidak mencukupi');
                }
            }
            $order->status = 'completed';
            $order->save();
        }
        return redirect()->route('order.history')->with('success', 'Checkout berhasil');
    }

    public function orderHistory()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Data customer tidak ditemukan');
        }

        $statuses = ['Paid', 'Kirim', 'Selesai'];

        // $orders = Order::where('customer_id', $customer->id)->whereIn('status', $statuses)->orderBy('id', 'desc')->get();
        $orders = Order::where('customer_id', $customer->id)->get();
        return view('v_order.history', compact('orders'));
    }

    public function removeFromCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        if ($order) {
            $orderItem = OrderItem::where('order_id', $order->id)->where('produk_id', $id)->first();

            if ($orderItem) {
                $order->total_harga -= $orderItem->harga * $orderItem->quantity;
                $orderItem->delete();

                if ($order->total_harga <= 0) {
                    $order->delete();
                } else {
                    $order->save();
                }
            }
        }
        return redirect()->route('order.cart')->with(
            'success',
            'Produk berhasil dihapus
dari keranjang',
        );
    }

    public function updateongkir(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        if ($order) {
            // Simpan data ongkir ke dalam order
            $order->kurir = $request->input('kurir');
            $order->layanan_ongkir = $request->input('layanan_ongkir');
            $order->biaya_ongkir = $request->input('biaya_ongkir');
            $order->estimasi_ongkir = $request->input('estimasi_ongkir');
            $order->total_berat = $request->input('total_berat');
            $order->alamat = $request->input('alamat') . ', <br>' . $request->input('city_name') . ', <br>' . $request->input('province_name');
            $order->pos = $request->input('pos');
            $order->save();
            return redirect()->route('order.selectpayment');
        }

        return back()->with('error', 'Gagal menyimpan data ongkir');
    }

    public function selectShipping(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return redirect()->route('order.cart')->with('error', 'Customer tidak ditemukan');
        }

        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->with('orderItems.produk')->first();

        if (!$order || $order->orderItems->count() == 0) {
            return redirect()->route('order.cart')->with('error', 'Keranjang kosong');
        }

        // 🔹 HITUNG TOTAL BERAT
        $totalWeight = $order->orderItems->sum(function ($item) {
            return $item->produk->berat * $item->quantity;
        });

        // 🔹 AMBIL PROVINCES (dari RajaOngkir)
        $provinces = [];
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        if ($response->successful()) {
            $provinces = $response->json()['data'] ?? [];
        }

        return view('v_order.select_shipping', compact('order', 'totalWeight', 'provinces'));
    }

    public function getProvinces()
    {
        $provinces = []; // ← tambahin ini
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        if ($response->successful()) {
            // Mengambil data provinsi dari respons JSON
            // Jika 'data' tidak ada, inisialisasi dengan array kosong
            $provinces = $response->json()['data'] ?? [];
        }

        // returning the view with provinces data
        return view('ongkir', compact('provinces'));
    }

    public function getCities($provinceId)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get(env('RAJAONGKIR_BASE_URL') . "/destination/city/{$provinceId}");

        if ($response->successful()) {
            // Mengambil data kota dari respons JSON
            // Jika 'data' tidak ada, inisialisasi dengan array kosong
            return response()->json($response->json()['data'] ?? []);
        }
    }

    public function getDistricts($cityId)
    {
        // Mengambil data kecamatan berdasarkan ID kota dari API Raja Ongkir
        $response = Http::withHeaders([
            //headers yang diperlukan untuk API Raja Ongkir
            'Accept' => 'application/json',
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->get(env('RAJAONGKIR_BASE_URL') . "/destination/district/{$cityId}");

        if ($response->successful()) {
            // Mengambil data kecamatan dari respons JSON
            // Jika 'data' tidak ada, inisialisasi dengan array kosong
            return response()->json($response->json()['data'] ?? []);
        }
    }

    public function checkOngkir(Request $request)
    {
        $response = Http::asForm()
            ->withHeaders([
                //headers yang diperlukan untuk API Raja Ongkir
                'Accept' => 'application/json',
                'key' => env('RAJAONGKIR_API_KEY'),
            ])
            ->post(env('RAJAONGKIR_BASE_URL') . '/calculate/domestic-cost', [
                'origin' => 6173,
                'destination' => $request->input('district_id'), // ID kecamatan tujuan
                'weight' => $request->input('weight'), // Berat dalam gram
                'courier' => $request->input('courier'), // Kode kurir (jne, tiki, pos)
            ]);

        if ($response->successful()) {
            // Mengambil data ongkos kirim dari respons JSON
            // Jika 'data' tidak ada, inisialisasi dengan array kosong
            return $response->json()['data'] ?? [];
        }
    }

    public function chooseShipping(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        // ✅ simpan ongkir lama dulu
        $oldOngkir = $order->biaya_ongkir ?? 0;

        // update data ongkir
        $order->kurir = $request->kurir;
        $order->layanan_ongkir = $request->service;
        $order->biaya_ongkir = $request->cost;
        $order->estimasi_ongkir = $request->etd;
        $order->total_berat = $request->weight;

        // ✅ hitung ulang total (hapus lama, tambah baru)
        $order->total_harga = $order->total_harga - $oldOngkir + $request->cost;

        $order->save();

        return redirect()->route('order.cart')->with('success', 'Pengiriman berhasil dipilih');
    }

    public function statusProses()
    {
        //backend
        // $order = Order::whereIn('status', ['Paid', 'Kirim'])
        //     ->orderBy('id', 'desc')
        //     ->get();

        $order = Order::with('customer.user')->orderBy('id','asc')->get();

        return view('backend.v_pesanan.proses', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'index' => $order,
        ]);
    }

    public function statusSelesai()
    {
        //backend
        $order = Order::where('status', 'Selesai')->orderBy('id', 'desc')->get();
        return view('backend.v_pesanan.selesai', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'judul' => 'Data Transaksi',
            'index' => $order,
        ]);
    }

    public function statusDetail($id)
    {
        $order = Order::findOrFail($id);
        return view('backend.v_pesanan.detail', [
            'subJudul' => 'Pesanan Proses',
            'judul' => 'Data Transaksi',
            'order' => $order,
        ]);
    }

    public function statusUpdate(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $rules = [
            'alamat' => 'required',
        ];
        if ($request->status != $order->status) {
            $rules['status'] = 'required';
        }
        if ($request->noresi != $order->noresi) {
            $rules['noresi'] = 'required';
        }
        if ($request->pos != $order->pos) {
            $rules['pos'] = 'required';
        }
        $validatedData = $request->validate($rules);
        Order::where('id', $id)->update($validatedData);
        return redirect()->route('backend.pesanan.proses')->with('success','Data berhasil diperbaharui',);
    }

   public function selectPayment(string $orderId)
{
    $customer = Customer::where('user_id', Auth::id())->first();

    if (!$customer) {
        return back()->with('error', 'Customer tidak ditemukan');
    }

    $order = Order::where('customer_id', $customer->id)
        ->where('id', $orderId)
        ->where('status', 'pending')
        ->first();

    if (!$order) {
        return back()->with('error', 'Order tidak ditemukan');
    }

    $order->load('orderItems.produk');

    // Hitung total harga
    $totalHarga = 0;
    foreach ($order->orderItems as $item) {
        $totalHarga += $item->harga * $item->quantity;
    }

    // Tambah ongkir (antisipasi null)
    $grossAmount = $totalHarga + ($order->biaya_ongkir ?? 0);

    // Midtrans config
    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    // order_id unik (JANGAN overwrite $orderId variable asli)
    $midtransOrderId = $order->id . '-' . time();

    $params = [
        'transaction_details' => [
            'order_id' => $midtransOrderId,
            'gross_amount' => (int) $grossAmount,
        ],
        'customer_details' => [
            'first_name' => $customer->user->name ?? 'Customer',
            'email' => $customer->user->email ?? 'test@mail.com',
            'phone' => $customer->hp ?? '-',
        ],
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return view('v_order.selectpayment', [
        'order' => $order,
        'snapToken' => $snapToken,
    ]);
}public function callback(Request $request)
{
    $serverKey = config('midtrans.server_key');

    $hashed = hash(
        'sha512',
        $request->order_id .
        $request->status_code .
        $request->gross_amount .
        $serverKey
    );

    if ($hashed == $request->signature_key) {

        $order = Order::find($request->order_id);

        if ($order) {

            $order->update([
                'status' => 'paid'
            ]);

            $produk = $order->produk;

            if ($produk && $produk->stok >= $order->qty) {

                $produk->decrement('stok', $order->qty);

            } else {

                Log::error("Stok tidak mencukupi untuk produk {$produk->id}");

            }
        }
    }

    return response()->json([
        'message' => 'Callback processed'
    ]);
}
    public function complete()
    {
        // Logika untuk halaman setelah pembayaran berhasil
        // return view('v_order.complete');
        return redirect()->route('order.history')->with('success', 'Checkout berhasil');
    }

    public function formOrderProses()
    {
        return view('backend.v_pesanan.formproses', [
            'judul' => 'Laporan',
            'subJudul' => 'Laporan Pesanan Proses',
        ]);
    }

    public function cetakOrderProses(Request $request)
    {
        // Menambahkan aturan validasi
        $request->validate(
            [
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            ],
            [
                'tanggal_awal.required' => 'Tanggal Awal harus diisi.',
                'tanggal_akhir.required' => 'Tanggal Akhir harus diisi.',
                'tanggal_akhir.after_or_equal' => 'Tanggal Akhir harus lebih besar atau sama
dengan Tanggal Awal.',
            ],
        );

        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $order = Order::whereIn('status', ['Paid', 'Kirim'])
            ->orderBy('id', 'desc')
            ->get();
        return view('backend.v_pesanan.cetakproses', [
            'judul' => 'Laporan',
            'subJudul' => 'Laporan Pesanan Proses',
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'cetak' => $order,
        ]);
    }

    public function formOrderSelesai()
    {
        return view('backend.v_pesanan.formselesai', [
            'judul' => 'Laporan',
            'subJudul' => 'Laporan Pesanan Selesai',
        ]);
    }

    public function cetakOrderSelesai(Request $request)
    {
        // Menambahkan aturan validasi
        $request->validate(
            [
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            ],
            [
                'tanggal_awal.required' => 'Tanggal Awal harus diisi.',
                'tanggal_akhir.required' => 'Tanggal Akhir harus diisi.',
                'tanggal_akhir.after_or_equal' => 'Tanggal Akhir harus lebih besar atau sama dengan Tanggal Awal.',
            ],
        );

        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        $order = Order::where('status', 'Selesai')
            ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('id', 'desc')
            ->get();

        $totalPendapatan = $order->sum(function ($row) {
            return $row->total_harga + $row->biaya_ongkir;
        });

        return view('backend.v_pesanan.cetakselesai', [
            'judul' => 'Laporan',
            'subJudul' => 'Laporan Pesanan Selesai',
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'cetak' => $order,
            'totalPendapatan' => $totalPendapatan,
        ]);
    }

    public function invoiceBackend($id)
    {
        $order = Order::findOrFail($id);
        return view('backend.v_pesanan.invoice', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'order' => $order,
        ]);
    }

    public function invoiceFrontend($id)
    {
        $order = Order::findOrFail($id);
        return view('backend.v_pesanan.invoice', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'order' => $order,
        ]);
    }
}
