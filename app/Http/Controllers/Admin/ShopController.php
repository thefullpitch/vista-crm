<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShopController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:shops.view|shops.create|shops.edit|shops.delete', only: ['index', 'show']),
            new Middleware('permission:shops.create', only: ['create', 'store']),
            new Middleware('permission:shops.edit', only: ['edit', 'update']),
            new Middleware('permission:shops.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Shop::with(['state.zone', 'owner']);
            return DataTables::of($data)
                ->addColumn('checkbox', function($row){
                    if(auth()->guard('admin')->user()->hasRole('Super Admin')) {
                        return '<input type="checkbox" name="shop_ids[]" value="'.$row->id.'" class="shop-checkbox">';
                    }
                    return '';
                })
                ->addIndexColumn()
                ->addColumn('contact_person', function($row){ return $row->owner->name ?? '-'; })
                ->addColumn('zone_name', function($row){ return $row->state->zone->name ?? '-'; })
                ->addColumn('state_name', function($row){ return $row->state->name ?? '-'; })
                ->editColumn('status', function($row){
                    $class = $row->status == 'Active' ? 'success' : 'secondary';
                    return '<span class="badge bg-'.$class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row){
                    $editUrl = route('admin.shops.edit', $row->id);
                    $deleteUrl = route('admin.shops.destroy', $row->id);
                    $showUrl = route('admin.shops.show', $row->id);
                    $btn = '<a href="'.$showUrl.'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>';
                    if(auth()->guard('admin')->user()->can('shops.edit')) {
                        $btn .= ' <a href="'.$editUrl.'" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i></a>';
                    }
                    if(auth()->guard('admin')->user()->can('shops.delete')) {
                        $btn .= ' <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\')">
                                    '.csrf_field().method_field("DELETE").'
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                  </form>';
                    }
                    return $btn;
                })
                ->rawColumns(['checkbox', 'action', 'status'])
                ->make(true);
        }
        return view('admin.business_network.shops.index');
    }

    public function create()
    {
        $states = State::all();
        return view('admin.business_network.shops.create', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firm_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'email' => 'required|email|max:255|unique:users,email',
            'state_id' => 'required|exists:states,id',

            'city_id' => 'required|exists:cities,id',
            'pincode_id' => 'required|exists:pincodes,id',
            'address' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $user = User::create([
            'name' => $request->contact_person,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make('password123'),
            'user_type' => 'Shop',
            'state_id' => $request->state_id,
            
            'city_id' => $request->city_id,
            'pincode_id' => $request->pincode_id,
            'address' => $request->address,
            'status' => $request->status == 'Active' ? 'Approved' : 'Pending',
        ]);

        Shop::create([
            'name' => $request->firm_name,
            'owner_id' => $user->id,
            'contact_number' => $request->mobile,
            'address' => $request->address,
            'state_id' => $request->state_id,
            
            'city_id' => $request->city_id,
            'pincode_id' => $request->pincode_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.shops.index')->with('success', 'Shop and User profile created successfully.');
    }

    public function show($id)
    {
        $shop = Shop::with(['state', 'city', 'pincode', 'owner'])->findOrFail($id);
        return view('admin.business_network.shops.show', compact('shop'));
    }

    public function edit(Shop $shop)
    {
        $states = State::all();
        $cities = \App\Models\City::where('state_id', $shop->state_id)->where('status', 1)->get();
        $pincodes = \App\Models\Pincode::where('city_id', $shop->city_id)->where('status', 1)->get();
        return view('admin.business_network.shops.edit', compact('shop', 'states', 'cities', 'pincodes'));
    }

    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'firm_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:users,mobile,'.$shop->owner_id,
            'email' => 'required|email|max:255|unique:users,email,'.$shop->owner_id,
            'state_id' => 'required|exists:states,id',

            'city_id' => 'required|exists:cities,id',
            'pincode_id' => 'required|exists:pincodes,id',
            'address' => 'required|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        if ($shop->owner) {
            $shop->owner->update([
                'name' => $request->contact_person,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'state_id' => $request->state_id,
                
                'city_id' => $request->city_id,
                'pincode_id' => $request->pincode_id,
                'address' => $request->address,
                'status' => $request->status == 'Active' ? 'Approved' : 'Pending',
            ]);
        }

        $shop->update([
            'name' => $request->firm_name,
            'contact_number' => $request->mobile,
            'address' => $request->address,
            'state_id' => $request->state_id,
            
            'city_id' => $request->city_id,
            'pincode_id' => $request->pincode_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.shops.index')->with('success', 'Shop and User profile updated successfully.');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return redirect()->route('admin.shops.index')->with('success', 'Shop deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin->hasRole('Super Admin')) {
            return response()->json(['success' => false, 'message' => 'Only Super Admin can perform bulk delete.']);
        }

        $ids = $request->input('ids');
        if($ids && is_array($ids)) {
            $shops = Shop::whereIn('id', $ids)->get();
            foreach($shops as $shop) {
                if($shop->owner_id) {
                    User::where('id', $shop->owner_id)->delete();
                }
                $shop->delete();
            }
            return response()->json(['success' => true, 'message' => 'Selected shops deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No shops selected.']);
    }

    public function downloadSample()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=shops_sample.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Firm Name', 'Contact Person', 'Mobile', 'Email', 'State', 'City', 'Pincode', 'Address', 'Status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            fputcsv($file, ['', 'Super Auto Spares', 'John Doe', '9876543210', 'john@example.com', 'Maharashtra', 'Mumbai', '400001', 'Street 1', 'Active']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=shops_export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Firm Name', 'Contact Person', 'Mobile', 'Email', 'State', 'City', 'Pincode', 'Address', 'Status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $shops = Shop::with(['owner', 'state', 'city', 'pincode'])->get();
            foreach ($shops as $shop) {
                fputcsv($file, [
                    $shop->id,
                    $shop->name,
                    $shop->owner->name ?? '',
                    $shop->contact_number,
                    $shop->owner->email ?? '',
                    $shop->state->name ?? '',
                    $shop->city->name ?? '',
                    $shop->pincode->pincode ?? '',
                    $shop->address,
                    $shop->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $fileHandle = fopen($file->getPathname(), 'r');
        fgetcsv($fileHandle); // Skip header

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($fileHandle)) !== false) {
            if (count($row) < 10) continue; 

            $id = $row[0];
            $firmName = trim($row[1]);
            $contactPerson = trim($row[2]);
            $mobile = trim($row[3]);
            $email = trim($row[4]);
            $stateName = trim($row[5]);
            $cityName = trim($row[6]);
            $pincodeVal = trim($row[7]);
            $address = trim($row[8]);
            $status = trim($row[9]) === 'Inactive' ? 'Inactive' : 'Active';

            if (empty($firmName) || empty($contactPerson) || empty($mobile) || empty($email) || empty($stateName) || empty($cityName) || empty($pincodeVal)) {
                continue;
            }

            $state = State::firstOrCreate(['name' => $stateName], ['status' => 1]);
            $city = \App\Models\City::firstOrCreate(['name' => $cityName, 'state_id' => $state->id], ['status' => 1]);
            $pincode = \App\Models\Pincode::firstOrCreate(['pincode' => $pincodeVal, 'city_id' => $city->id], ['status' => 1]);

            if (!empty($id)) {
                $shop = Shop::find($id);
                if ($shop) {
                    $shop->update([
                        'name' => $firmName,
                        'contact_number' => $mobile,
                        'address' => $address,
                        'state_id' => $state->id,
                        'city_id' => $city->id,
                        'pincode_id' => $pincode->id,
                        'status' => $status
                    ]);
                    if($shop->owner) {
                        $shop->owner->update([
                            'name' => $contactPerson,
                            'mobile' => $mobile,
                            'email' => $email,
                            'state_id' => $state->id,
                            'city_id' => $city->id,
                            'pincode_id' => $pincode->id,
                            'address' => $address,
                            'status' => $status == 'Active' ? 'Approved' : 'Pending',
                        ]);
                    }
                    $updated++;
                }
            } else {
                $user = User::firstOrCreate(
                    ['mobile' => $mobile],
                    [
                        'name' => $contactPerson,
                        'email' => $email,
                        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                        'user_type' => 'Shop',
                        'state_id' => $state->id,
                        'city_id' => $city->id,
                        'pincode_id' => $pincode->id,
                        'address' => $address,
                        'status' => $status == 'Active' ? 'Approved' : 'Pending',
                    ]
                );

                Shop::create([
                    'name' => $firmName,
                    'owner_id' => $user->id,
                    'contact_number' => $mobile,
                    'address' => $address,
                    'state_id' => $state->id,
                    'city_id' => $city->id,
                    'pincode_id' => $pincode->id,
                    'status' => $status
                ]);
                $imported++;
            }
        }
        fclose($fileHandle);
        return redirect()->back()->with('success', "CSV Processed successfully. Imported: $imported, Updated: $updated.");
    }
}
