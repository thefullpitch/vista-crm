<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KycRecord;
use App\Models\Invoice;
use App\Models\Installation;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $shopBoysCount = User::where('user_type', 'Shop Boy')->count();
        $shopBoysApproved = User::where('user_type', 'Shop Boy')->whereIn('status', ['Active', 'Approved', '1'])->count();
        $shopBoysPending = User::where('user_type', 'Shop Boy')->where('status', 'Pending')->count();
        $shopBoysSuspended = User::where('user_type', 'Shop Boy')->where('status', 'Suspended')->count();

        $installersCount = User::where('user_type', 'Installer')->count();
        $installersApproved = User::where('user_type', 'Installer')->whereIn('status', ['Active', 'Approved', '1'])->count();
        $installersPending = User::where('user_type', 'Installer')->where('status', 'Pending')->count();
        $installersSuspended = User::where('user_type', 'Installer')->where('status', 'Suspended')->count();

        $womenEntrepreneursCount = User::where('user_type', 'Women Entrepreneurs')->count();
        $womenEntrepreneursApproved = User::where('user_type', 'Women Entrepreneurs')->whereIn('status', ['Active', 'Approved', '1'])->count();
        $womenEntrepreneursPending = User::where('user_type', 'Women Entrepreneurs')->where('status', 'Pending')->count();
        $womenEntrepreneursSuspended = User::where('user_type', 'Women Entrepreneurs')->where('status', 'Suspended')->count();

        $pendingKyc = KycRecord::where('status', 'Pending')->count();
        $pendingInvoices = Invoice::where('status', 'Pending')->count();
        
        $totalPointsIssued = Invoice::where('status', 'Verified')->sum('points_earned') + Installation::where('status', 'Verified')->sum('points_earned');
        
        // Chart Data (Last 6 Months Registration)
        $months = collect([]);
        $usersData = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months->push($month->format('M'));
            $usersData->push(User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count());
        }

        // Recent Data Lists
        $recentUsers = User::latest()->take(5)->get();
        $recentInvoices = Invoice::with('user')->latest()->take(5)->get();

        // Zone-wise User Count
        $userTypeFilter = $request->input('user_type');
        $zoneUsersQuery = User::selectRaw('zones.name as zone_name, count(users.id) as total')
            ->join('states', 'users.state_id', '=', 'states.id')
            ->join('zones', 'states.zone_id', '=', 'zones.id');
            
        if ($userTypeFilter) {
            $zoneUsersQuery->where('users.user_type', $userTypeFilter);
        }

        $zoneUsers = $zoneUsersQuery->groupBy('zones.name')->get();
        
        $zoneLabels = $zoneUsers->pluck('zone_name');
        $zoneData = $zoneUsers->pluck('total');

        // Zone-wise Sales (Amount) - Selected Month
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);
        
        $zoneSalesQuery = Invoice::selectRaw('zones.name as zone_name, sum(invoices.amount) as total_sales')
            ->join('users', 'invoices.user_id', '=', 'users.id')
            ->join('states', 'users.state_id', '=', 'states.id')
            ->join('zones', 'states.zone_id', '=', 'zones.id')
            ->where('invoices.status', 'Verified')
            ->whereMonth('invoices.created_at', $currentMonth)
            ->whereYear('invoices.created_at', $currentYear)
            ->groupBy('zones.name')
            ->get();

        $zoneSalesLabels = $zoneSalesQuery->pluck('zone_name');
        $zoneSalesData = $zoneSalesQuery->pluck('total_sales');

        // Zone-wise Shop Count
        $zoneShops = \App\Models\Shop::selectRaw('zones.name as zone_name, count(shops.id) as total')
            ->join('states', 'shops.state_id', '=', 'states.id')
            ->join('zones', 'states.zone_id', '=', 'zones.id')
            ->groupBy('zones.name')
            ->get();
        
        $zoneShopLabels = $zoneShops->pluck('zone_name');
        $zoneShopData = $zoneShops->pluck('total');

        return view('admin.dashboard', compact(
            'totalUsers', 'shopBoysCount', 'installersCount', 'womenEntrepreneursCount',
            'shopBoysApproved', 'shopBoysPending', 'shopBoysSuspended',
            'installersApproved', 'installersPending', 'installersSuspended',
            'womenEntrepreneursApproved', 'womenEntrepreneursPending', 'womenEntrepreneursSuspended',
            'pendingKyc', 'pendingInvoices', 'totalPointsIssued', 
            'months', 'usersData', 'recentUsers', 'recentInvoices',
            'zoneLabels', 'zoneData', 'zoneSalesLabels', 'zoneSalesData',
            'zoneShopLabels', 'zoneShopData',
            'currentMonth', 'currentYear', 'userTypeFilter'
        ));
    }
}
