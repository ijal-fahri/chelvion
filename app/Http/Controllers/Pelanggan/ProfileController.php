<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
// [BARU] Tambahkan ini
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Pastikan ini mengarah ke file view kustom Anda
        // Saya asumsikan lokasinya 'pelanggan.profile.edit'
        return view('profile.edit', [ 
            'user' => $request->user(),
            // Anda mungkin perlu variabel ini untuk navbar
            'cartCount' => \App\Models\Cart::where('user_id', $request->user()->id)->count(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // [DIUBAH] Kita gunakan validasi manual karena ProfileUpdateRequest tidak ada 'photo'
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] // Validasi foto
        ]);
        
        // Isi data nama dan email
        $user->fill($request->only('name', 'email'));

        // Handle jika email diubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // [BARU] Logika untuk menyimpan foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            
            // Simpan foto baru
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        // Redirect kembali dengan pesan sukses
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}