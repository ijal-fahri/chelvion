<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\OfflineOrder;
use App\Models\Order;
use App\Models\TradeIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LaporanKasirController extends Controller
{
    /**
     * Display the main report page with dynamic data.
     */
    public function index()
    {
        $kasir = Auth::user();
        $cabangId = $kasir->cabang_id;
        
        if (!$cabangId) {
            return redirect()->route('kasir.dashboard')->with('error', 'Anda tidak terhubung ke cabang manapun.');
        }

        // --- Fetch Offline Data ---
        $offlineOrders = OfflineOrder::where('cabang_id', $cabangId)
            ->with('items')
            ->latest()
            ->get();

        // Transform ke array biasa untuk menghindari masalah getKey()
        $offlineTransactions = [];
        foreach ($offlineOrders as $order) {
            if (!is_object($order)) {
                Log::warning("Data OfflineOrder bukan object:", ['data' => $order]);
                continue;
            }
            
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'name' => $item->product_name . ($item->variant_info ? ' (' . $item->variant_info . ')' : ''),
                    'qty' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal
                ];
            }
            
            $offlineTransactions[] = [
                'id' => $order->invoice_number,
                'customer' => $order->customer_name ?? 'Walk-in',
                'phone' => $order->customer_phone,
                'email' => $order->customer_email,
                'date' => $order->created_at->toIso8601String(),
                'method' => $order->payment_method,
                'subtotal' => (float) $order->total_amount,
                'discount_amount' => 0,
                'voucher_code' => null,
                'total' => (float) $order->total_amount,
                'items' => $items,
                'type' => 'offline',
                'is_offline' => true,
            ];
        }

        // --- Fetch Online Data ---
        $onlineOrders = Order::where(function ($query) use ($cabangId) {
                $query->where('shipping_cabang_id', $cabangId)
                      ->orWhere('pickup_cabang_id', $cabangId);
            })
            ->where('status', 'Selesai')
            ->with(['items', 'user'])
            ->latest()
            ->get();

        $onlineTransactions = [];
        foreach ($onlineOrders as $order) {
            if (!$order instanceof \App\Models\Order) {
                Log::error("Data Order Online bukan instance Model Order yang valid.", [
                    'expected_class' => \App\Models\Order::class,
                    'actual_type' => gettype($order),
                    'data_dump' => $order
                ]);
                continue;
            }

            try {
                $email = optional($order->user)->email ?? 'N/A';
                $customerName = optional($order->user)->name ?? $order->receiver_name ?? 'Pelanggan Online';
                
                $subtotal = 0;
                $items = [];
                
                if ($order->relationLoaded('items') && $order->items) {
                    foreach ($order->items as $item) {
                        $itemSubtotal = $item->price * $item->quantity;
                        $subtotal += $itemSubtotal;
                        
                        $items[] = [
                            'name' => $item->product_name . ($item->variant_info ? ' (' . $item->variant_info . ')' : ''),
                            'qty' => $item->quantity,
                            'price' => (float) $item->price,
                            'subtotal' => (float) $itemSubtotal
                        ];
                    }
                }

                $onlineTransactions[] = [
                    'id' => $order->order_number,
                    'customer' => $customerName,
                    'phone' => $order->phone_number,
                    'email' => $email,
                    'date' => $order->created_at->toIso8601String(),
                    'method' => $order->payment_method,
                    'subtotal' => (float) $subtotal,
                    'discount_amount' => (float) $order->discount_amount,
                    'voucher_code' => $order->voucher_code,
                    'total' => (float) $order->total_price,
                    'items' => $items,
                    'type' => 'online',
                    'is_offline' => false,
                ];
            } catch (\Throwable $e) {
                Log::error("Error saat mapping data Order Online ID: " . ($order->id ?? 'N/A'), [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // --- Combine and Sort ---
        // Menggunakan array_merge dan usort untuk menghindari masalah Collection
        $transactions = array_merge($offlineTransactions, $onlineTransactions);
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // --- Fetch Trade-in Data ---
        $tradeIns = TradeIn::where('cabang_id', $cabangId)
            ->latest()
            ->get();

        $tradeInData = [];
        foreach ($tradeIns as $trade) {
            if (!is_object($trade)) continue;
            
            $tradeInData[] = [
                'id' => $trade->id,
                'date' => $trade->created_at->toIso8601String(),
            ];
        }

        // --- Send to View ---
        return view('kasir.laporan.index', [
            'transactions' => $transactions,
            'tradeIns' => $tradeInData
        ]);
    }

    /**
     * Create and download the report in PDF format.
     */
    public function downloadPDF(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'month' => 'required|string',
        ]);

        $year = $validated['year'];
        $month = $validated['month'];

        $kasir = Auth::user();
        $cabangId = $kasir->cabang_id;
        
        if (!$cabangId) {
            abort(403, 'User tidak terhubung ke cabang.');
        }
        
        $cabangName = optional($kasir->cabang)->nama_cabang ?? 'Cabang Tidak Diketahui';

        // --- Query Data (Offline & Online) ---
        $offlineOrdersQuery = OfflineOrder::where('cabang_id', $cabangId)->whereYear('created_at', $year);
        $onlineOrdersQuery = Order::where(function($q) use ($cabangId) {
                $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId);
            })
            ->where('status', 'Selesai')
            ->whereYear('created_at', $year);

        // Apply Month Filter
        $monthName = 'Semua Bulan';
        $monthNumber = null;
        
        if ($month !== 'all') {
            if (ctype_digit($month) && $month >= 0 && $month <= 11) {
                $monthNumber = (int)$month + 1;
                $offlineOrdersQuery->whereMonth('created_at', $monthNumber);
                $onlineOrdersQuery->whereMonth('created_at', $monthNumber);
                
                try {
                    $monthName = Carbon::create()->month($monthNumber)->locale('id')->isoFormat('MMMM');
                } catch (\Exception $e) {
                    Log::warning("Gagal format nama bulan untuk PDF: " . $month);
                    $monthName = 'Bulan ' . $monthNumber;
                }
            }
        }

        $offlineOrders = $offlineOrdersQuery->with('items')->get();
        $onlineOrders = $onlineOrdersQuery->with(['user', 'items'])->get();

        // --- Format Data for PDF ---
        $allTransactions = [];
        
        foreach ($offlineOrders as $order) {
            if (!is_object($order)) continue;
            
            $itemsSummary = '';
            foreach ($order->items as $it) {
                $itemsSummary .= $it->product_name . ' (x' . $it->quantity . '), ';
            }
            $itemsSummary = rtrim($itemsSummary, ', ');
            
            $allTransactions[] = (object)[
                'id' => $order->invoice_number,
                'date' => $order->created_at,
                'customer' => $order->customer_name ?? 'Walk-in',
                'method' => $order->payment_method,
                'subtotal' => $order->total_amount,
                'discount' => 0,
                'total' => $order->total_amount,
                'type' => 'Offline',
                'items_summary' => $itemsSummary
            ];
        }
        
        foreach ($onlineOrders as $order) {
            if (!$order instanceof \App\Models\Order) continue;
            
            try {
                $subtotal = 0;
                $itemsSummary = '';
                
                if ($order->relationLoaded('items') && $order->items) {
                    foreach ($order->items as $item) {
                        $subtotal += $item->price * $item->quantity;
                        $itemsSummary .= $item->product_name . ' (x' . $item->quantity . '), ';
                    }
                    $itemsSummary = rtrim($itemsSummary, ', ');
                }
                
                $allTransactions[] = (object)[
                    'id' => $order->order_number,
                    'date' => $order->created_at,
                    'customer' => optional($order->user)->name ?? $order->receiver_name ?? 'Online',
                    'method' => $order->payment_method,
                    'subtotal' => $subtotal,
                    'discount' => $order->discount_amount,
                    'total' => $order->total_price,
                    'type' => 'Online',
                    'items_summary' => $itemsSummary
                ];
            } catch (\Throwable $e) {
                Log::error("Error saat mapping PDF untuk Order Online ID: " . ($order->id ?? 'N/A'), [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Sort by date descending
        usort($allTransactions, function($a, $b) {
            return $b->date <=> $a->date;
        });

        // --- Fetch and Format Trade-in Data ---
        $tradeInsQuery = TradeIn::where('cabang_id', $cabangId)->whereYear('created_at', $year);
        if ($monthNumber) {
            $tradeInsQuery->whereMonth('created_at', $monthNumber);
        }
        
        $filteredTradeIns = $tradeInsQuery->get();

        // --- Prepare Summary Data ---
        $totalRevenue = array_sum(array_column($allTransactions, 'total'));
        $transactionCount = count($allTransactions);
        $tradeInCount = $filteredTradeIns->count();
        $reportTitle = "Laporan Penjualan - {$monthName} {$year}";
        $period = "{$monthName} {$year}";

        $dataForPdf = compact(
            'allTransactions',
            'filteredTradeIns',
            'reportTitle',
            'totalRevenue',
            'transactionCount',
            'tradeInCount',
            'cabangName',
            'period'
        );

        // --- Generate PDF ---
        try {
            $pdf = PDF::loadView('kasir.laporan.pdf_template', $dataForPdf)
                      ->setPaper('a4', 'landscape');

            $fileName = "laporan-{$cabangName}-{$year}-" . 
                       ($month === 'all' ? 'semua' : str_pad($monthNumber, 2, '0', STR_PAD_LEFT)) . 
                       ".pdf";
            
            $fileName = preg_replace('/[\\\\\/:*?"<>|]+/', '-', $fileName);
            $fileName = trim($fileName, '-');

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error("Gagal generate PDF laporan ({$reportTitle}): " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            return back()->with('error', 'Gagal membuat laporan PDF. Terjadi kesalahan internal.');
        }
    }
}