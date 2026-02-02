<?php

namespace App\Http\Controllers;

use App\Desk;
use App\Location;
use App\DeskPart;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\ItemType;

class DeskController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $desks = Desk::with('items')->get();

        // Use pluck to get ['id' => 'name'] format for the dropdown
        $locations = Location::pluck('name', 'id');

        return view('desks.index', [
            'desks' => $desks,
            'locations' => $locations,
        ]);
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
        $this->validate($request, [
            'title' => [
                'required',
                'string',
                'min:2',
                // Check uniqueness of title WHERE location is the same as input
                Rule::unique('desks')->where(function ($query) use ($request) {
                    return $query->where('location_id', $request->location_id);
                }),
            ],
            'location_id' => 'required'
        ]);

        $request['user_id'] = Auth::id();

        $desk = Desk::create($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Desk Created',
                    'id' => $desk->id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Eager load deskparts and the items within those deskparts
        $desk = Desk::with(['location', 'deskparts.items'])->findOrFail($id);

        // 2. Generate the QR Code for the current URL
        // size(150) sets the dimensions, margin(1) removes excess white space
        $qrcode = QrCode::size(150)
                ->margin(1)
                ->generate(url()->current());
        $item_types = ItemType::orderBy('name', 'ASC')->pluck('name', 'id');

        // 3. Pass the $qrcode variable to the view
        return view('desks.show', compact('desk', 'qrcode', 'item_types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $desk = Desk::find($id);
        return $desk;
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
            'title' => [
                'required',
                'string',
                'min:2',
                        Rule::unique('desks')
                        ->where(function ($query) use ($request) {
                            return $query->where('location_id', $request->location_id);
                        })
                        ->ignore($id), // Ignore the current record
            ],
            'location_id' => 'required'
        ]);

        $desk = Desk::findOrFail($id);
        $desk->update($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Desk Updated',
                    'id' => $desk->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $desk = Desk::findOrFail($id);

        // 1. Check for DeskParts
        if ($desk->deskparts()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This desk still contains ' . $desk->deskparts()->count() . ' deskparts. Please delete the deskparts first.'
                            ], 422);
        }
        // 2. Check for Items (Directly linked to Desk)
        if ($desk->items()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This desk is currently linked to ' . $desk->items()->count() . ' items.'
                            ], 422);
        }
        // 3. If empty, proceed
        $desk->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Desk Deleted Successfully'
        ]);
    }

    public function storeDeskPart(Request $request) {
        $this->validate($request, [
            'desk_id' => 'required|exists:desks,id',
            'title' => [
                'required',
                'string',
                'min:1',
                // Check uniqueness of title WHERE desk_id is the same as input
                Rule::unique('deskparts')->where(function ($query) use ($request) {
                    return $query->where('desk_id', $request->desk_id);
                })->ignore($request->id), // Ignore current ID if we are editing
            ],
        ]);

        // Use updateOrCreate to handle both Add and Edit
        DeskPart::updateOrCreate(
                ['id' => $request->id],
                [
                    'title' => $request->title,
                    'desk_id' => $request->desk_id
                ]
        );

        return response()->json([
                    'success' => true,
                    'message' => 'DeskPart Saved Successfully'
        ]);
    }

    public function deleteDeskPart($id) {
        $deskpart = DeskPart::findOrFail($id);

        // Safety Check: Prevent deletion if items are still inside this deskpart
        if ($deskpart->items()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This deskpart still contains ' . $deskpart->items()->count() . ' items.'
                            ], 422);
        }

        $deskpart->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'DeskPart Deleted'
        ]);
    }

    public function apiDesks() {
        $desks = Desk::with('location')->withCount('deskparts');

        return Datatables::of($desks)
                        ->addColumn('location', function ($desk) {
                            if ($desk->location) {
                                return '<span class="label label-info"><i class="fa fa-map-marker"></i> ' . $desk->location->name . '</span>';
                            }
                            return '<span class="label label-danger">No Location</span>';
                        })
                        ->addColumn('action', function ($desk) {
                            // FIXED: Changed '+' to '.' for PHP string concatenation
                            return '<div class="">' .
                                    '<a href="' . route('desks.show', $desk->id) . '" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a> ' .
                                    '<a onclick="printLabel(' . $desk->id . ')" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-print"></i></a> ' .
                                    '<a onclick="editForm(' . $desk->id . ')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> </a> ' .
                                    '<a onclick="deleteData(' . $desk->id . ')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> </a>' .
                                    '</div>';
                        })
                        ->rawColumns(['location', 'action'])
                        ->make(true);
    }

// Fetch the full directory tree for one specific desk when expanded
    public function getDeskDetails($id) {
        return Desk::with(['deskparts.items'])->findOrFail($id);
    }
}
