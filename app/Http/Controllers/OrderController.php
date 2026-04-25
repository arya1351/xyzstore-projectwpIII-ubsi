<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function addToCart($id)
    {
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
    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending', 'paid')->first();
        if ($order) {
            $order->load('orderItems.produk');
        }
        return view('v_order.cart', compact('order'));
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
    // ongkir getProvinces
    //     public function getProvinces()
    //     {
    //         $response = Http::withHeaders([
    //             'key' => env('RAJAONGKIR_API_KEY')
    //         ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

    //         return response()->json($response->json());
    //     }

    //     // ongkir getCities
    //     public function getCities(Request $request)
    //     {
    //         $provinceId = $request->input('province_id');
    //         $response = Http::withHeaders([
    //             'key' => env('RAJAONGKIR_API_KEY')
    //         ])->get(env('RAJAONGKIR_BASE_URL') . '/city', [
    //             'province' => $provinceId
    //         ]);

    //         return response()->json($response->json());
    //     }

    //     // ongkir getCost
    //     public function getCost(Request $request)
    //   {
    //         $origin = $request->input('origin');
    //         $destination = $request->input('destination');
    //         $weight = $request->input('weight');
    //         $courier = $request->input('courier');

    //         $response = Http::withHeaders([
    //             'key' => env('RAJAONGKIR_API_KEY')
    //         ])->post(env('RAJAONGKIR_BASE_URL') . '/cost', [
    //             'origin' => $origin,
    //             'destination' => $destination,
    //             'weight' => $weight,
    //             'courier' => $courier,
    //         ]);

    //         return response()->json($response->json());
    //     }

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
}
