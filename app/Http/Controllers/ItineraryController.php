<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\Destination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItineraryController extends Controller
{
  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'start_date' => 'required|date',
    ]);

    $user = $request->user();

    DB::beginTransaction();

    try {
      // 2. LOGIKA MENGHITUNG ESTIMATED PRICE
      // Ambil semua destination_id unik yang dikirim dari request
      $destinationIds = collect($validated['items'])->pluck('destination_id')->unique();

      // Ambil harga (price) dari tabel destinations berdasarkan ID di atas
      $destinations = Destination::whereIn('id', $destinationIds)->pluck('price', 'id');

      // Lakukan perulangan untuk menjumlahkan harga destinasi
      $totalEstimatedPrice = 0;
      foreach ($validated['items'] as $item) {
        // Tambahkan harga destinasi ke total. Jika tidak ada harga, anggap 0
        $totalEstimatedPrice += $destinations[$item['destination_id']] ?? 0;
      }

      // 3. Simpan data utama Itinerary ke Database
      $itinerary = Itinerary::create([
        'user_id' => $user->id, // Mengambil ID user yang sedang login/terautentikasi
        'title' => $validated['title'],
        'start_date' => $validated['start_date'],
        'estimated_price' => $totalEstimatedPrice, // Hasil hitungan dimasukkan di sini
      ]);

      // 4. Simpan semua Itinerary Items melalui relasi (itinerary_id otomatis terisi)
      $itinerary->itineraryItems()->createMany($validated['items']);

      // Jika semua lancar, simpan permanen ke database
      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => 'Itinerary dan item perjalanan berhasil dibuat!',
        'data' => $itinerary->load('itineraryItems.destination') // Memuat data beserta detail destinasinya
      ], 201);
    } catch (\Exception $e) {
      // Jika ada error di tengah jalan, batalkan semua data yang sempat masuk
      DB::rollBack();

      // Log error untuk kebutuhan debugging developer
      Log::error('Gagal membuat itinerary: ' . $e->getMessage());

      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan sistem saat menyimpan itinerary.',
        'debug' => config('app.debug') ? $e->getMessage() : null
      ], 500);
    }
  }
}
