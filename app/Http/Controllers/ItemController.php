<?php

namespace App\Http\Controllers;

use App\Item;
use App\Cabinet;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\ItemLog;
use App\Location;
use App\ItemType;

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
        $locations = Location::orderBy('name', 'ASC')->pluck('name', 'id');
        // Fetch cabinets for the dropdown
        $cabinets = Cabinet::orderBy('title', 'ASC')->pluck('title', 'id');
        $item_types = ItemType::orderBy('name', 'ASC')->pluck('name', 'id');
        return view('items.index', compact('cabinets', 'locations', 'item_types'));
    }

    public function tokens(Request $request) {
        // 1. Start the query with necessary relationships for the breadcrumbs
        $query = Item::with(['user', 'cabinet.location', 'drawer']);

        // 2. Filter by Search / Name or Serial Number
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('serial_number', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Filter by Item Type (The string field)
        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // 4. Execute with Pagination
        $items = $query->latest()->paginate(20);

        return view('items.tokens', compact('items'));
    }

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
        // 1. Validation Logic
        $request->validate([
            'name' => 'required|string|max:255',
            'item_type' => 'required|string',
            'qty' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'trackable' => 'required|in:Yes,No',
            // Permanent Requirement
            'location_id' => 'required|exists:locations,id',
            // Conditional Requirements
//            'location' => 'required_if:trackable,No|nullable|string|max:255',
            'cabinet_id' => 'required_if:trackable,Yes|nullable|exists:cabinets,id',
            'drawer_id' => 'required_if:trackable,Yes|nullable|exists:drawers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $input = $request->all();
        $input['user_id'] = Auth::id();

        // 2. Data Integrity Cleanup
        if ($request->trackable === 'No') {
            // If not trackable, we only keep location text; clear the IDs
            $input['cabinet_id'] = null;
            $input['drawer_id'] = null;
        }

        // 3. Image Uploading
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::slug($request->name, '-') . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/items'), $fileName);
            $input['image'] = $fileName;
        }

        // 4. Create record
        $item = Item::create($input);

        // 5. Create Log
        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'message' => Auth::user()->name . ' Added ' . $item->name,
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
        $item = Item::with(['user', 'itemLocation', 'cabinet', 'drawer', 'itemType'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $locations = Location::orderBy('name', 'ASC')->pluck('name', 'id');
        // Fetch cabinets for the dropdown
        $cabinets = Cabinet::orderBy('title', 'ASC')->pluck('title', 'id');
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
        $item = Item::findOrFail($id);

        // 1. Validation Logic (Matches Store)
        $request->validate([
            'name' => 'required|string|max:255',
            'item_type' => 'required|string',
            'qty' => 'required|numeric|min:0',
            'trackable' => 'required|in:Yes,No',
            // Permanent Requirement
            'location_id' => 'required|exists:locations,id',
            // Conditional Requirements
//            'location' => 'required_if:trackable,No|nullable|string|max:255',
            'cabinet_id' => 'required_if:trackable,Yes|nullable|exists:cabinets,id',
            'drawer_id' => 'required_if:trackable,Yes|nullable|exists:drawers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $input = $request->all();

        // 2. Data Integrity Cleanup
        if ($request->trackable === 'No') {
            $input['cabinet_id'] = null;
            $input['drawer_id'] = null;
        }

        // 3. Image Uploading
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image && file_exists(public_path('upload/items/' . $item->image))) {
                @unlink(public_path('upload/items/' . $item->image));
            }

            $file = $request->file('image');
            $fileName = Str::slug($request->name, '-') . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/items'), $fileName);
            $input['image'] = $fileName;
        } else {
            // Keep existing image if no new file is uploaded
            $input['image'] = $item->image;
        }

        // 4. Update record
        $item->update($input);

        // 5. Create Log
        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'message' => Auth::user()->name . ' Updated ' . $item->name,
        ]);

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

    public function apiItemsORIGIN() {
        // Eager load itemLocation (the relationship), cabinet, and drawer
        $items = Item::with(['user', 'itemLocation', 'cabinet', 'drawer'])->select('items.*');

        return Datatables::of($items)
                        ->addColumn('by', function ($item) {
                            return optional($item->user)->name ?? 'System';
                        })
                        ->addColumn('location', function ($item) {
                            $html = '<i class="fa fa-map-marker text-muted"></i> ';

                            // 1. Base Location (Always Required)
                            if ($item->itemLocation) {
                                $html .= '<a href="' . route('locations.show', $item->itemLocation->id) . '">' . e($item->itemLocation->name) . '</a>';
                            } else {
                                $html .= '<span class="text-muted">No Location</span>';
                            }

                            // 2. Sub-Location logic
                            if ($item->trackable === 'Yes') {
                                // Trackable Path: Site > Cabinet > Drawer
                                if ($item->cabinet) {
                                    $html .= ' <i class="fa fa-angle-right" style="margin:0 2px;"></i> ';
                                    $html .= '<a href="' . route('cabinets.show', $item->cabinet->id) . '">' . e($item->cabinet->title) . '</a>';
                                }
                                if ($item->drawer) {
                                    $html .= ' <i class="fa fa-angle-right" style="margin:0 2px;"></i> ';
                                    $html .= '<span class="text-muted">' . e($item->drawer->title) . '</span>';
                                }
                            }

                            return $html;
                        })
                        ->addColumn('serial_number', function ($item) {
                            return '<a href="' . route('items.show', $item->id) . '" class="btn btn-link btn-xs">'
                                    . e($item->serial_number) . '</a>';
                        })
                        ->addColumn('show_photo', function ($item) {
                            return '<img class="img-thumbnail" width="50" src="' . $item->show_photo . '" alt="Photo">';
                        })
                        ->addColumn('action', function ($item) {
                            return '
                <a href="' . route('items.show', $item->id) . '" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a>
                <a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
                <a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
            ';
                        })
                        // IMPORTANT: Add 'location' to rawColumns so the HTML/Icons render correctly
                        ->rawColumns(['show_photo', 'action', 'serial_number', 'location'])
                        ->make(true);
    }

    public function apiItems() {
        // 1. Added 'itemType' to the eager loading list
        $items = Item::with(['user', 'itemLocation', 'cabinet', 'drawer', 'itemType'])->select('items.*');

        return Datatables::of($items)
                        ->addColumn('by', function ($item) {
                            return optional($item->user)->name ?? 'System';
                        })
                        // 2. Added the Item Type column logic
                        ->addColumn('item_type', function ($item) {
                            return optional($item->itemType)->name ?? '<span class="label label-default">N/A</span>';
                        })
                        ->addColumn('location', function ($item) {
                            $html = '<i class="fa fa-map-marker text-muted"></i> ';

                            // Base Location
                            if ($item->itemLocation) {
                                $html .= '<a href="' . route('locations.show', $item->itemLocation->id) . '">' . e($item->itemLocation->name) . '</a>';
                            } else {
                                $html .= '<span class="text-muted">No Location</span>';
                            }

                            // Sub-Location logic
                            if ($item->trackable === 'Yes') {
                                if ($item->cabinet) {
                                    $html .= ' <i class="fa fa-angle-right" style="margin:0 2px;"></i> ';
                                    $html .= '<a href="' . route('cabinets.show', $item->cabinet->id) . '">' . e($item->cabinet->title) . '</a>';
                                }
                                if ($item->drawer) {
                                    $html .= ' <i class="fa fa-angle-right" style="margin:0 2px;"></i> ';
                                    $html .= '<span class="text-muted">' . e($item->drawer->title) . '</span>';
                                }
                            }
                            return $html;
                        })
                        ->addColumn('serial_number', function ($item) {
                            return '<a href="' . route('items.show', $item->id) . '" class="btn btn-link btn-xs">'
                                    . e($item->serial_number) . '</a>';
                        })
                        ->addColumn('show_photo', function ($item) {
                            return '<img class="img-thumbnail" width="50" src="' . $item->show_photo . '" alt="Photo">';
                        })
                        ->addColumn('action', function ($item) {
                            return '
                <a href="' . route('items.show', $item->id) . '" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a>
                <a onclick="editForm(' . $item->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
                <a onclick="deleteData(' . $item->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
            ';
                        })
                        // 3. Added 'item_type' to rawColumns in case you use labels/html
                        ->rawColumns(['show_photo', 'action', 'serial_number', 'location', 'item_type'])
                        ->make(true);
    }

    public function getCabinets($location_id) {
        // Assuming Cabinet model has a 'location_id' foreign key
        $cabinets = Cabinet::where('location_id', $location_id)->orderBy('title', 'ASC')->get(['id', 'title']);
        return response()->json($cabinets);
    }

    public function getDrawers($cabinet_id) {
        // Assuming Drawer model has a 'cabinet_id' foreign key
        // Replace 'App\Drawer' with your actual Drawer model path
        $drawers = \App\Drawer::where('cabinet_id', $cabinet_id)->orderBy('title', 'ASC')->get(['id', 'title']);
        return response()->json($drawers);
    }
}
