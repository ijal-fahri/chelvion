<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Cabang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OwnerDataVoucherController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('owner.datavoucher.index', compact('cabangs'));
    }

    public function getData()
    {
        try {
            $vouchers = Voucher::with('cabang')->latest()->get()->map(function ($voucher) {
                $status = 'Aktif';
                if ($voucher->expiry_date && $voucher->expiry_date->isPast()) {
                    $status = 'Kedaluwarsa';
                } elseif ($voucher->stock !== null && $voucher->times_used >= $voucher->stock) {
                    $status = 'Habis';
                }

                return [
                    'id' => $voucher->id,
                    'name' => $voucher->name,
                    'description' => $voucher->description,
                    'code' => $voucher->code,
                    
                    // --- [PERUBAHAN] Kirim semua data untuk modal edit ---
                    'type' => $voucher->type,
                    'discount_percentage' => $voucher->discount_percentage,
                    'discount_amount' => $voucher->discount_amount,
                    'min_purchase' => $voucher->min_purchase,
                    'max_discount' => $voucher->max_discount,
                    // [UBAH] Ganti nama 'discount' agar lebih jelas
                    'discount_display' => $voucher->type == 'percentage' ? $voucher->discount_percentage . '%' : 'Rp' . number_format($voucher->discount_amount, 0, ',', '.'),
                    // --- [AKHIR PERUBAHAN] ---

                    'expiry_date' => $voucher->expiry_date ? $voucher->expiry_date->format('Y-m-d') : null,
                    'applicable_branch' => $voucher->cabang->nama_cabang ?? 'Semua Cabang',
                    'cabang_id' => $voucher->cabang_id,
                    'stock' => $voucher->stock,
                    'times_used' => $voucher->times_used,
                    'status_label' => $status,
                ];
            });

            $activeVouchers = $vouchers->where('status_label', 'Aktif');
            $expiringSoon = $activeVouchers->filter(function ($v) {
                return $v['expiry_date'] && now()->diffInDays($v['expiry_date'], false) <= 7 && now()->diffInDays($v['expiry_date'], false) >= 0;
            })->sortBy('expiry_date');
            
            // --- [PERUBAHAN] Logika distribusi diskon ---
            $discountDist = [ "Potongan Tetap" => 0, "1-10%" => 0, "11-25%" => 0, ">25%" => 0 ];
            foreach ($activeVouchers as $v) {
                if ($v['type'] == 'fixed') {
                    $discountDist["Potongan Tetap"]++;
                } else {
                    if ($v['discount_percentage'] <= 10) $discountDist["1-10%"]++;
                    else if ($v['discount_percentage'] <= 25) $discountDist["11-25%"]++;
                    else $discountDist[">25%"]++;
                }
            }
            // --- [AKHIR PERUBAHAN] ---

            $cabangs = Cabang::withCount(['vouchers' => fn($q) => $q->where(fn($q2) => $q2->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()))])->get();
            $allBranchVouchers = Voucher::whereNull('cabang_id')->where(fn($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()))->count();
            
            return response()->json([
                'vouchers' => $vouchers->keyBy('id'),
                'active_voucher_count' => $activeVouchers->count(),
                'discount_distribution' => $discountDist,
                'expiring_soon' => $expiringSoon->values(),
                'branch_performance' => $cabangs,
                'all_branch_vouchers' => $allBranchVouchers,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting voucher data: '. $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data voucher.'], 500);
        }
    }

    // --- [PERUBAHAN] Validasi di Store dan Update ---
    private function validateVoucher(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cabang_id' => [
                'required',
                'string',
                Rule::when($request->input('cabang_id') !== 'all', ['exists:cabangs,id']),
            ],
            'type' => 'required|in:percentage,fixed',
            
            // Validasi kondisional
            'discount_percentage' => 'nullable|numeric|min:0.01|max:100|required_if:type,percentage',
            'max_discount' => 'nullable|numeric|min:0|required_if:type,percentage',
            'discount_amount' => 'nullable|numeric|min:1|required_if:type,fixed',
            'min_purchase' => 'nullable|numeric|min:0',

            'stock' => 'nullable|integer|min:0|required_if:has_stock,on',
            'expiry_date' => 'nullable|date|after_or_equal:today|required_if:has_expiry,on',
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->validateVoucher($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Voucher::create([
            'name' => $request->name,
            'code' => strtoupper(Str::random(8)),
            'description' => $request->description,
            'cabang_id' => $request->cabang_id === 'all' ? null : $request->cabang_id,
            
            // --- [PERUBAHAN] Simpan data baru ---
            'type' => $request->type,
            'discount_percentage' => $request->type == 'percentage' ? $request->discount_percentage : null,
            'discount_amount' => $request->type == 'fixed' ? $request->discount_amount : null,
            'min_purchase' => $request->min_purchase ?? 0,
            'max_discount' => $request->type == 'percentage' ? $request->max_discount : null,
            // --- [AKHIR PERUBAHAN] ---

            'stock' => $request->has('has_stock') ? $request->stock : null,
            'expiry_date' => $request->has('has_expiry') ? $request->expiry_date : null,
        ]);

        return response()->json(['success' => 'Voucher berhasil dibuat.'], 201);
    }

    public function update(Request $request, Voucher $voucher)
    {
         $validator = $this->validateVoucher($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $voucher->update([
            'name' => $request->name,
            'description' => $request->description,
            'cabang_id' => $request->cabang_id === 'all' ? null : $request->cabang_id,
            
            // --- [PERUBAHAN] Simpan data baru ---
            'type' => $request->type,
            'discount_percentage' => $request->type == 'percentage' ? $request->discount_percentage : null,
            'discount_amount' => $request->type == 'fixed' ? $request->discount_amount : null,
            'min_purchase' => $request->min_purchase ?? 0,
            'max_discount' => $request->type == 'percentage' ? $request->max_discount : null,
            // --- [AKHIR PERUBAHAN] ---

            'stock' => $request->has('has_stock') ? $request->stock : null,
            'expiry_date' => $request->has('has_expiry') ? $request->expiry_date : null,
        ]);

        return response()->json(['success' => 'Voucher berhasil diperbarui.']);
    }
    // --- [AKHIR PERUBAHAN] ---

    public function destroy(Voucher $voucher)
    {
        try {
            $voucher->delete();
            return response()->json(['success' => 'Voucher berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting voucher: '.$e->getMessage());
            return response()->json(['error' => 'Gagal menghapus voucher.'], 500);
        }
    }
}