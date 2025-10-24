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
use Illuminate\Support\Facades\Log;

class OwnerDataKaryawanController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen data karyawan.
     */
    public function index()
    {
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('owner.datakaryawan.index', compact('cabangs'));
    }

    /**
     * [API] Mengambil semua data karyawan (kasir & staf gudang).
     */
    public function getData()
    {
        $karyawan = User::whereIn('usertype', ['kasir', 'staf_gudang'])
            ->with('cabang')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'name' => $k->name,
                    'email' => $k->email,
                    'peran' => $k->usertype === 'kasir' ? 'Kasir' : 'Staff Gudang',
                    'usertype' => $k->usertype,
                    'cabang' => $k->cabang->nama_cabang ?? null,
                    'cabang_id' => $k->cabang_id,
                    'photo' => $k->photo ? asset('storage/' . $k->photo) : null,
                    'status' => $k->status_karyawan,
                    'joining_date' => $k->created_at->toDateString(),
                    'contract_end' => $k->contract_end,
                ];
            });
        
        $stats = [
            'total' => $karyawan->count(),
            'tetap' => $karyawan->where('status', 'Tetap')->count(),
            'kontrak' => $karyawan->where('status', 'Kontrak')->count(),
        ];

        $peranDistribution = $karyawan->groupBy('peran')->map->count();
        $cabangDistribution = $karyawan->whereNotNull('cabang')->groupBy('cabang')->map->count();

        return response()->json([
            'karyawan' => $karyawan->keyBy('id'),
            'stats' => $stats,
            'peran_chart' => [
                'labels' => $peranDistribution->keys(),
                'values' => $peranDistribution->values(),
            ],
            'cabang_chart' => [
                'labels' => $cabangDistribution->keys(),
                'values' => $cabangDistribution->values(),
            ],
        ]);
    }

    /**
     * Menyimpan karyawan baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'usertype' => 'required|in:kasir,staf_gudang',
            'cabang_id' => 'required|exists:cabangs,id',
            'status' => 'required|in:Tetap,Kontrak',
            'contract_end' => 'nullable|date|required_if:status,Kontrak|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($request->password);
        $data['status_karyawan'] = $data['status'];
        $data['joining_date'] = now();
        unset($data['status']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('avatars', 'public');
        }

        User::create($data);

        return response()->json(['success' => 'Karyawan baru berhasil ditambahkan.'], 201);
    }

    /**
     * Mengupdate data karyawan.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'usertype' => 'required|in:kasir,staf_gudang',
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

        return response()->json(['success' => 'Data karyawan berhasil diperbarui.']);
    }

    /**
     * Menghapus data karyawan.
     */
    public function destroy(User $user)
    {
        if (!in_array($user->usertype, ['kasir', 'staf_gudang'])) {
             return response()->json(['error' => 'Hanya karyawan yang bisa dihapus.'], 403);
        }

        try {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->delete();
            return response()->json(['success' => 'Karyawan berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting employee: '.$e->getMessage());
            return response()->json(['error' => 'Gagal menghapus karyawan.'], 500);
        }
    }
}
