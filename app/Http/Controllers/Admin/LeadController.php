<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Admin;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LeadController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:leads.view|leads.edit', only: ['index', 'show']),
            new Middleware('permission:leads.edit', only: ['updateStatus']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Lead::with(['user', 'assignee'])->whereHas('user', function($q) {
                $q->where('user_type', 'Women Entrepreneurs');
            });

            $admin = auth()->guard('admin')->user();
            if (!$admin->hasRole('Super Admin')) {
                if (!empty($admin->user_types) && count($admin->user_types) > 0) {
                    $data->whereHas('user', function($q) use ($admin) {
                        $q->whereIn('user_type', $admin->user_types);
                    });
                }
                if (!empty($admin->state_ids) && count($admin->state_ids) > 0) {
                    $data->whereHas('user', function($q) use ($admin) {
                        $q->whereIn('state_id', $admin->state_ids);
                    });
                } elseif ($admin->zone_id) {
                    $data->whereHas('user.state', function($q) use ($admin) {
                        $q->where('zone_id', $admin->zone_id);
                    });
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function($row){ return $row->user->name ?? '-'; })
                ->addColumn('assignee_name', function($row){ return $row->assignee->name ?? 'Unassigned'; })
                ->editColumn('status', function($row){
                    $class = 'secondary';
                    if($row->status == 'In Progress') $class = 'warning';
                    if($row->status == 'Converted') $class = 'success';
                    if($row->status == 'Closed') $class = 'danger';
                    return '<span class="badge bg-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('points_earned', function($row){
                    if ($row->points_earned > 0) {
                        return '<span class="fw-bold text-success">'.$row->points_earned.' pts</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function($row){
                    $showUrl = route('admin.leads.show', $row->id);
                    return '<a href="'.$showUrl.'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i> Manage</a>';
                })
                ->rawColumns(['action', 'status', 'points_earned'])
                ->make(true);
        }
        return view('admin.leads.index');
    }

    public function show($id)
    {
        $lead = Lead::with(['user', 'assignee'])->findOrFail($id);
        $admins = Admin::whereHas('roles', function($q) {
            $q->where('name', 'Support Executive');
        })->get(); 
        return view('admin.leads.show', compact('lead', 'admins'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:New,In Progress,Converted,Closed',
            'admin_remarks' => 'nullable|string',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $lead = Lead::with('user')->findOrFail($id);
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $lead->status = $request->status;
            
            if ($request->has('admin_remarks')) {
                $lead->admin_remarks = $request->admin_remarks;
            }

            if ($request->hasFile('invoice_file')) {
                $file = $request->file('invoice_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/invoices'), $filename);
                $lead->invoice_file = 'uploads/invoices/' . $filename;
            }
            
            // Handle points for In Progress
            if ($lead->status == 'In Progress' && !$lead->awarded_progress_points) {
                $progressPoints = (int) (\App\Models\Setting::where('key', 'points_earn_lead_progress')->value('value') ?? 50);
                if ($progressPoints > 0 && $lead->user) {
                    $lead->points_earned += $progressPoints;
                    $lead->awarded_progress_points = true;
                    $lead->user->wallet_balance += $progressPoints;
                    $lead->user->save();
                }
            }

            // Handle points for Converted
            if ($lead->status == 'Converted' && !$lead->awarded_converted_points) {
                $convertedPoints = (int) (\App\Models\Setting::where('key', 'points_earn_lead_converted')->value('value') ?? 200);
                if ($convertedPoints > 0 && $lead->user) {
                    $lead->points_earned += $convertedPoints;
                    $lead->awarded_converted_points = true;
                    $lead->user->wallet_balance += $convertedPoints;
                    $lead->user->save();
                }
            }

            $lead->save();
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Lead updated successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update lead: ' . $e->getMessage());
        }
    }
}
