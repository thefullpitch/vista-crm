<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Invoice;
use App\Models\Installation;
use App\Models\RedemptionRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.view', only: ['index', 'exportUsers', 'exportInvoices']),
        ];
    }

    public function index()
    {
        // Key Metrics
        $totalMechanics = User::count();
        $totalShops = Shop::count();
        $totalPointsAwarded = Invoice::where('status', 'Verified')->sum('points_earned') 
                            + Installation::where('status', 'Verified')->sum('points_earned');
        $totalRedemptions = RedemptionRequest::whereIn('status', ['Approved', 'Shipped', 'Delivered'])->count();

        return view('admin.reports.index', compact(
            'totalMechanics', 
            'totalShops', 
            'totalPointsAwarded', 
            'totalRedemptions'
        ));
    }

    public function exportUsers()
    {
        $users = User::with('kyc')->get();

        $filename = "mechanics_export_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Mobile', 'Language', 'Status', 'Wallet Balance', 'KYC Status', 'Registered At']);

            foreach ($users as $user) {
                $kycStatus = $user->kyc ? $user->kyc->status : 'Not Submitted';
                $status = $user->status == 1 ? 'Active' : 'Suspended';
                
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->mobile,
                    $user->preferred_language,
                    $status,
                    $user->wallet_balance,
                    $kycStatus,
                    $user->created_at->format('Y-m-d H:i')
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportInvoices()
    {
        $invoices = Invoice::with(['user', 'shop'])->get();

        $filename = "invoices_export_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Mechanic', 'Shop', 'Invoice No', 'Date', 'Amount', 'Status', 'Points Earned', 'Submitted At']);

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->id,
                    $inv->user->name ?? 'N/A',
                    $inv->shop->firm_name ?? 'N/A',
                    $inv->invoice_number,
                    $inv->invoice_date,
                    $inv->amount,
                    $inv->status,
                    $inv->points_earned,
                    $inv->created_at->format('Y-m-d H:i')
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
