<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:products.view', only: ['index', 'show']),
            new Middleware('permission:products.create', only: ['create', 'store']),
            new Middleware('permission:products.edit', only: ['edit', 'update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $products = Product::with(['category', 'subcategory'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'Active')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'reward_points' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'Active')->orWhere('id', $product->category_id)->get();
        $subcategories = Subcategory::where('category_id', $product->category_id)->get();
        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'reward_points' => 'nullable|integer|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function downloadSample()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products_sample.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Category', 'Subcategory', 'Product Name', 'Price', 'Reward Points', 'Status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Sample row 1
            fputcsv($file, ['', 'Electronics', 'Mobile Phones', 'iPhone 15', '799.99', '10', 'Active']);
            // Sample row 2 (update example)
            fputcsv($file, ['1', 'Electronics', 'Mobile Phones', 'iPhone 15 Pro', '999.99', '20', 'Active']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products_export.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Category', 'Subcategory', 'Product Name', 'Price', 'Reward Points', 'Status'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $products = Product::with(['category', 'subcategory'])->get();
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->category->name ?? '',
                    $product->subcategory->name ?? '',
                    $product->name,
                    $product->price,
                    $product->reward_points,
                    $product->status
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
        $header = fgetcsv($fileHandle); // Skip header

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($fileHandle)) !== false) {
            if (count($row) < 6) continue; // Ensure required columns exist

            $id = $row[0];
            $categoryName = trim($row[1]);
            $subcategoryName = trim($row[2]);
            $productName = trim($row[3]);
            $price = trim($row[4]);
            $rewardPoints = isset($row[5]) && is_numeric(trim($row[5])) ? (int)trim($row[5]) : 0;
            
            // If the old CSV format without reward points is uploaded, status is at index 5.
            $statusIndex = isset($row[6]) ? 6 : 5;
            $status = trim($row[$statusIndex]) === 'Inactive' ? 'Inactive' : 'Active';

            if (empty($categoryName) || empty($subcategoryName) || empty($productName) || empty($price)) {
                continue;
            }

            // Find or create Category
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['status' => 'Active']
            );

            // Find or create Subcategory
            $subcategory = Subcategory::firstOrCreate(
                ['name' => $subcategoryName, 'category_id' => $category->id],
                ['status' => 'Active']
            );

            if (!empty($id)) {
                // Update
                $product = Product::find($id);
                if ($product) {
                    $product->update([
                        'category_id' => $category->id,
                        'subcategory_id' => $subcategory->id,
                        'name' => $productName,
                        'price' => $price,
                        'reward_points' => $rewardPoints,
                        'status' => $status
                    ]);
                    $updated++;
                }
            } else {
                // Create
                Product::create([
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'name' => $productName,
                    'price' => $price,
                    'reward_points' => $rewardPoints,
                    'status' => $status
                ]);
                $imported++;
            }
        }

        fclose($fileHandle);

        return redirect()->back()->with('success', "CSV Processed successfully. Imported: $imported, Updated: $updated.");
    }
}
