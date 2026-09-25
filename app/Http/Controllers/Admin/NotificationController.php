<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:notifications.view|notifications.create', only: ['index']),
            new Middleware('permission:notifications.create', only: ['create', 'store', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Notification::with('user');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('recipient', function($row){ return $row->user->name ?? 'Deleted User'; })
                ->editColumn('is_read', function($row){
                    return $row->is_read ? '<span class="badge bg-success">Read</span>' : '<span class="badge bg-secondary">Unread</span>';
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('admin.notifications.destroy', $row->id);
                    $btn = '<form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this notification log?\')">
                                '.csrf_field().method_field("DELETE").'
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
                              </form>';
                    return $btn;
                })
                ->rawColumns(['action', 'is_read'])
                ->make(true);
        }
        return view('admin.notifications.index');
    }

    public function create()
    {
        $users = User::where('status', 1)->get(['id', 'name', 'mobile']);
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required', // Can be 'all' or specific ID
            'title' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            if ($request->user_id == 'all') {
                $users = User::where('status', 1)->pluck('id');
                $notifications = [];
                $now = now();
                
                // Batch insert for performance
                foreach ($users as $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => $request->title,
                        'message' => $request->message,
                        'is_read' => 0,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                
                // Chunk inserts if there are too many users
                foreach (array_chunk($notifications, 500) as $chunk) {
                    Notification::insert($chunk);
                }
                
                $msg = 'Notification broadcasted to all active mechanics successfully.';
            } else {
                $request->validate(['user_id' => 'exists:users,id']);
                Notification::create([
                    'user_id' => $request->user_id,
                    'title' => $request->title,
                    'message' => $request->message,
                    'is_read' => 0
                ]);
                $msg = 'Notification sent successfully to selected mechanic.';
            }

            DB::commit();
            return redirect()->route('admin.notifications.index')->with('success', $msg);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to send notification: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully.');
    }
}
