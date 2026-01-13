<?php

namespace App\Http\Controllers;

use App\Category;
use App\Item;
use App\Cabinet;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\ItemLog;

class ItemController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $category = Category::orderBy('name', 'ASC')->pluck('name', 'id');
        // Fetch cabinets for the dropdown
        $cabinets = Cabinet::orderBy('title', 'ASC')->pluck('title', 'id');

        return view('items.index', compact('category', 'cabinets'));
    }

    public function tokens(Request $request) {
        // 1. Start the query (do not use ->get() yet)
        $query = Item::with(['category', 'user']);

        // 2. Filter by Search / Name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 4. Filter by Condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // 5. Execute with Pagination (better for lists)
        $items = $query->latest()->paginate(20);

        // Also fetch categories for the dropdown menu
        $categories = \App\Category::all();

        return view('items.tokens', compact('items', 'categories'));
    }

//    public function tokens() {
//        $items = Item::with('category', 'user')->get();
//        return view('items.tokens', compact('items'));
//    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // 1. Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'condition' => 'nullable|string',
            'instructions' => 'nullable|string',
            'trackable' => 'required|in:Yes,No',
            'location' => 'required_if:trackable,No|nullable|string',
            'cabinet_id' => 'required_if:trackable,Yes|nullable|exists:cabinets,id',
            'drawer_id' => 'required_if:trackable,Yes|nullable|exists:drawers,id',
        ]);

        // Initialize input with validated data
        $input = $validatedData;
        $input['user_id'] = Auth::id();

        // 2. Optimized Image Handling
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Create a unique name: name-timestamp.extension
            $fileName = Str::slug($request->name, '-') . '-' . time() . '.' . $file->getClientOriginalExtension();

            // Move file to public/upload/items
            $file->move(public_path('upload/items'), $fileName);

            // Save only the filename to the database
            $input['image'] = $fileName;
        }

        // 3. Create record (Capture the object in $item)
        $item = Item::create($input);

        // 4. Create Log (Fixed variable references)
        ItemLog::create([
            'item_id' => $item->id, // Now $item is defined
            'user_id' => Auth::id(),
            'message' => Auth::user()->name . ' Added ' . $item->name, // Fixed Auth::name and title
        ]);

        return response()->json([
                    'success' => true,
                    'message' => 'Item Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $item = Item::with(['category', 'user', 'cabinet', 'drawer'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $category = Category::orderBy('name', 'ASC')
                ->get()
                ->pluck('name', 'id');
        $item = Item::find($id);
        return $item;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'qty' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'trackable' => 'required|in:Yes,No',
            'location' => 'required_if:trackable,No|nullable|string',
            'cabinet_id' => 'required_if:trackable,Yes|nullable|exists:cabinets,id',
            'drawer_id' => 'required_if:trackable,Yes|nullable|exists:drawers,id',
        ]);

        $item = Item::findOrFail($id);
        $input = $request->except('image');

        if ($request->hasFile('image')) {
            // 1. Delete old image if it exists
            // We use trim to ensure no leading slashes cause issues with public_path()
            if ($item->image && file_exists(public_path(trim($item->image, '/')))) {
                unlink(public_path(trim($item->image, '/')));
            }

            // 2. Prepare new image
            $fileName = Str::slug($request->name) . '-' . time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('upload/items'), $fileName);

            // 3. Save relative path (standard practice)
            $input['image'] = 'upload/items/' . $fileName;
        }

        $item->update($input);

        return response()->json([
                    'success' => true,
                    'message' => 'Item Updated Successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $item = Item::findOrFail($id);

        // Check if the image field is not empty and the file actually exists on the disk
        if (!empty($item->image)) {
            $path = public_path(trim($item->image, '/'));
            if (is_file($path)) {
                unlink($path);
            }
        }

        $item->delete(); // Use delete() on the instance since we already found it

        return response()->json([
                    'success' => true,
                    'message' => 'Item and associated image deleted'
        ]);
    }

    public function apiItems() {
        // Use Eager Loading (with) to speed up the database
        // Use query() instead of all() for better DataTables performance
        $items = Item::with(['category', 'user', 'cabinet', 'drawer'])->select('items.*');

        return Datatables::of($items)
                        ->addColumn('category_name', function ($item) {
                            // Use optional() to prevent crashes if category is missing
                            return optional($item->category)->name ?? 'Uncategorized';
                        })
                        ->addColumn('by', function ($item) {
                            return optional($item->user)->name ?? 'System';
                        })
                        ->addColumn('location', function ($item) {
                            // 1. Check if trackable is Yes
                            if ($item->trackable == 'Yes') {
                                $cabinet = $item->cabinet->title ?? 'N/A';
                                $drawer = $item->drawer->title ?? 'N/A';
                                return $cabinet . ' [' . $drawer . ']';
                            }

                            // 2. Return default location if No
                            return $item->location;
                        })
                        ->addColumn('serial_number', function ($item) {
                            return '<a href="' . route('items.show', $item->id) . '" class="btn btn-link btn-xs">'
                                    . $item->serial_number . '</a>';
                        })
                        ->addColumn('show_photo', function ($item) {
                            return '<img class="rounded-square" width="50" height="50" src="' . $item->show_photo . '" alt="">';
                        })
                        ->addColumn('action', function ($item) {
                            return '<a href="' . route('items.show', $item->id) . '" class="btn btn-info btn-xs">' .
                                    '<i class="glyphicon glyphicon-eye-open"></i> Show</a> ' .
                                    '<a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs">' .
                                    '<i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs">' .
                                    '<i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        // Combine all raw columns here
                        ->rawColumns(['show_photo', 'action', 'serial_number'])
                        ->make(true);
    }
}
