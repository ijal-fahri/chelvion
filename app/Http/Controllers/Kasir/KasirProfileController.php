<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// [BARU] Tambahkan ini untuk mengelola file
use Illuminate\Support\Facades\Storage; 

class KasirProfileController extends Controller
{
    /**
     * Menampilkan halaman profil kasir yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        return view('kasir.profile.index', compact('user'));
    }

    /**
     * Memperbarui informasi profil kasir.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // [DIUBAH] Validasi diperbarui
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id) // Email harus unik, kecuali untuk user ini
            ],
            'phone' => [
                'nullable', 
                'string', 
                'max:20', 
                Rule::unique('users')->ignore($user->id)
            ],
            'photo' => [ // [BARU] Validasi untuk foto
                'nullable',
                'image',       // Harus file gambar
                'mimes:jpg,jpeg,png', // Hanya format ini
                'max:2048'     // Maksimal 2MB
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Siapkan data untuk diupdate
        $updateData = $request->only('name', 'email', 'phone');

        // [BARU] Logika untuk menangani upload foto
        if ($request->hasFile('photo')) {
            // 1. Simpan foto lama untuk dihapus nanti
            $oldPhoto = $user->photo;

            // 2. Simpan foto baru di 'storage/app/public/profile_photos'
            $path = $request->file('photo')->store('profile_photos', 'public');

            // 3. Tambahkan path foto baru ke data update
            $updateData['photo'] = $path;

            // 4. Hapus foto lama jika ada
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        // Update data user
        $user->update($updateData);

        // Refresh model user untuk mendapatkan 'photo_url' accessor terbaru
        $user->refresh();

        // Kirim respon JSON sukses dengan data user yang baru
        return response()->json([
            'success' => 'Profil berhasil diperbarui!',
            'user' => $user // 'user' sekarang otomatis berisi 'photo_url'
        ]);
    }
}