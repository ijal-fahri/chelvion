<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OwnerDataAdminController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen data admin.
     */
    public function index()
    {
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('owner.dataadmin.index', compact('cabangs'));
    }

    /**
     * [API] Mengambil semua data admin untuk ditampilkan.
     */
    public function getData()
    {
        $admins = User::where('usertype', 'admin')
            ->with('cabang')
            ->get()
            ->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => 'Admin Cabang',
                    'cabang' => $admin->cabang->nama_cabang ?? null,
                    'cabang_id' => $admin->cabang_id,
                    'photo' => $admin->photo ? asset('storage/' . $admin->photo) : null,
                    'status' => $admin->status_karyawan,
                    'joining_date' => $admin->created_at->toDateString(), 
                    'contract_end' => $admin->contract_end,
                ];
            });
        
        $stats = [
            'total' => $admins->count(),
            'tetap' => $admins->where('status', 'Tetap')->count(),
            'kontrak' => $admins->where('status', 'Kontrak')->count(),
        ];

        $cabangDistribution = $admins->whereNotNull('cabang')->groupBy('cabang')->map->count();

        return response()->json([
            'admins' => $admins->keyBy('id'),
            'stats' => $stats,
            'cabang_chart' => [
                'labels' => $cabangDistribution->keys(),
                'values' => $cabangDistribution->values(),
            ],
        ]);
    }

    /**
     * Menyimpan admin baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', // 'confirmed' dihapus
            'cabang_id' => 'required|exists:cabangs,id',
            'status' => 'required|in:Tetap,Kontrak',
            'contract_end' => 'nullable|date|required_if:status,Kontrak|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['usertype'] = 'admin';
        $data['password'] = Hash::make($request->password);
        $data['status_karyawan'] = $data['status'];
        $data['joining_date'] = now(); 
        unset($data['status']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        User::create($data);

        return response()->json(['success' => 'Admin baru berhasil ditambahkan.'], 201);
    }

    /**
     * Mengupdate data admin.
     */
    public function update(Request $request, User $user)
    {
        if ($user->usertype === 'owner') {
            return response()->json(['error' => 'Data Owner tidak dapat diubah.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8', // 'confirmed' dihapus
            'cabang_id' => 'required|exists:cabangs,id',
            'status' => 'required|in:Tetap,Kontrak',
            'contract_end' => 'nullable|date|required_if:status,Kontrak|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['status_karyawan'] = $data['status'];
        unset($data['status']);
        
        if ($data['status_karyawan'] === 'Tetap') {
            $data['contract_end'] = null;
        }
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json(['success' => 'Data admin berhasil diperbarui.']);
    }

    /**
     * Menghapus data admin.
     */
    public function destroy(User $user)
    {
        if ($user->usertype === 'owner') {
            return response()->json(['error' => 'Owner tidak dapat dihapus.'], 403);
        }

        try {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->delete();
            return response()->json(['success' => 'Admin berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting admin: '.$e->getMessage());
            return response()->json(['error' => 'Gagal menghapus admin.'], 500);
        }
    }
}

