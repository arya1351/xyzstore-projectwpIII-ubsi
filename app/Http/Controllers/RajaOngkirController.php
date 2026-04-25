<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
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
            ->post(env('RAJAONGKIR_BASE_URL') ."/calculate/domestic-cost", [
                'origin' => 3855, // ID kecamatan Diwek (ganti sesuai kebutuhan)
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
}
// public function getCities(Request $request)
// {
//     $provinceName = $request->input('province_name');

//     $response = Http::withHeaders([
//         'key' => env('RAJAONGKIR_API_KEY'),
//     ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/domestic-destination', [
//         'search' => $provinceName, // 🔥 WAJIB isi nama provinsi
//         'limit' => 1000,
//     ]);

//     $data = $response->json();

//     // // 🔥 ambil data kota unik
//     // $filtered = collect($data['data'])->unique('city_name')->values();

//     return response()->json([
//         // 'data' => $filtered,
//         'status_code' => $response->status(),
//         'raw_body' => $response->body(),
//         'json' => $response->json(),
//     ]);
// }

// public function getDistricts(Request $request)
// {
//     $cityId = $request->input('city_id');

//     $response = Http::withHeaders([
//         'key' => env('RAJAONGKIR_API_KEY'),
//     ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/domestic-destination', [
//         'search' => '',
//         'limit' => 1000,
//     ]);

//     $data = $response->json();

//     $filtered = collect($data['data'])->where('city_id', $cityId)->values();

//     return response()->json([
//         'data' => $filtered,
//     ]);
// }

// public function getCost(Request $request)
// {
//     $origin = $request->input('origin');
//     $destination = $request->input('destination');
//     $weight = $request->input('weight');
//     $courier = $request->input('courier');
//     $response = Http::withHeaders([
//         'key' => env('RAJAONGKIR_API_KEY'),
//     ])->post(env('RAJAONGKIR_BASE_URL') . '/cost', [
//         'origin' => $origin,
//         'destination' => $destination,
//         'weight' => $weight,
//         'courier' => $courier,
//     ]);
//     return response()->json($response->json());
// }
