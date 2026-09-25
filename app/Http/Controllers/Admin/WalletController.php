<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTransaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WalletController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:rewards.view|rewards.edit', only: ['index', 'show']),
            new Middleware('permission:rewards.edit', only: ['addPoints', 'deductPoints']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');
            
            if ($request->filled('user_type')) {
                $data->where('user_type', $request->user_type);
            }
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('wallet_balance', function($row){
                    return '<span class="fw-bold text-success">'.number_format($row->wallet_balance).' pts</span>';
                })
                ->addColumn('action', function($row){
                    return '<a href="'.route('admin.wallets.show', $row->id).'" class="btn btn-primary btn-sm"><i class="bi bi-wallet2"></i> Manage Wallet</a>';
                })
                ->rawColumns(['action', 'wallet_balance'])
                ->make(true);
        }
        
        $userTypes = User::select('user_type')->whereNotNull('user_type')->distinct()->pluck('user_type');
        return view('admin.wallets.index', compact('userTypes'));
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($request->ajax()) {
            $data = WalletTransaction::where('user_id', $id)->orderBy('created_at', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('transaction_type', function($row){
                    if($row->transaction_type == 'Credit') {
                        return '<span class="badge bg-success"><i class="bi bi-arrow-down-circle"></i> Credit</span>';
                    }
                    return '<span class="badge bg-danger"><i class="bi bi-arrow-up-circle"></i> Debit</span>';
                })
                ->editColumn('amount', function($row){
                    $color = $row->transaction_type == 'Credit' ? 'text-success' : 'text-danger';
                    $sign = $row->transaction_type == 'Credit' ? '+' : '-';
                    return '<strong class="'.$color.'">'.$sign.number_format($row->amount).'</strong>';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('M d, Y h:i A');
                })
                ->rawColumns(['transaction_type', 'amount'])
                ->make(true);
        }

        $invoices = \App\Models\Invoice::with(['items'])->where('user_id', $id)->where('status', 'Verified')->orderBy('created_at', 'desc')->get();
        
        return view('admin.wallets.show', compact('user', 'invoices'));
    }

    public function addPoints(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($id);
        
        DB::transaction(function() use ($user, $request) {
            $user->wallet_balance += $request->amount;
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'Credit',
                'amount' => $request->amount,
                'balance_after' => $user->wallet_balance,
                'description' => $request->description,
                'reference_type' => 'manual',
            ]);
        });

        return redirect()->back()->with('success', 'Points successfully added to wallet.');
    }

    public function deductPoints(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($id);

        if ($user->wallet_balance < $request->amount) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient wallet balance for this deduction.']);
        }

        DB::transaction(function() use ($user, $request) {
            $user->wallet_balance -= $request->amount;
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'transaction_type' => 'Debit',
                'amount' => $request->amount,
                'balance_after' => $user->wallet_balance,
                'description' => $request->description,
                'reference_type' => 'manual',
            ]);
        });

        return redirect()->back()->with('success', 'Points successfully deducted from wallet.');
    }
}
