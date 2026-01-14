<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $locations = Location::with('cabinets')->get();
        return view('locations.index');
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
            'name' => 'required|string|min:2|unique:locations,name',
        ]);

        $request['user_id'] = Auth::id();

        Location::create($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Categories Created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $location = Location::find($id);
        return $location;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // 1. Add unique validation but IGNORE this specific $id
        $this->validate($request, [
            'name' => 'required|string|min:2|unique:locations,name,' . $id
        ]);

        // 2. Find the existing location
        $location = Location::findOrFail($id);

        // 3. Update with the validated data
        $location->update($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Location Updated' // Changed from "Categories Update"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $location = Location::findOrFail($id);

        // 1. Check if there are any items associated with this location
        // This assumes you have a 'items' relationship in your Location Model
        if ($location->cabinets()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This location is currently being used by ' . $location->cabinets()->count() . ' cabinets.'
                            ], 422); // We send 422 (Unprocessable Entity) to trigger an error in AJAX
        }

        // 2. If no items exist, proceed with deletion
        $location->delete();
        return response()->json([
                    'success' => true,
                    'message' => 'Location Deleted'
        ]);
    }

    public function apiLocations() {
        // Eager load cabinets and items count
        $locations = Location::with('cabinets')->get();

        return Datatables::of($locations)
                        ->addColumn('cabinets', function ($location) {
                            $links = [];
                            foreach ($location->cabinets as $cabinet) {
                                // Assuming your route is named 'cabinets.show'
                                $url = route('cabinets.show', $cabinet->id);
                                $links[] = '<a href="' . $url . '" class="label label-warning">' . $cabinet->name . '</a>';
                            }
                            // Join all links with a space or comma
                            return implode(' ', $links);
                        })
                        ->addColumn('action', function ($location) {
                            return '<a onclick="editForm(' . $location->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $location->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['cabinets', 'action']) // Ensure 'cabinets' is treated as HTML
                        ->make(true);
    }

    public function apiLocationsORIGIN() {
        // withCount('items') adds a 'items_count' attribute to each location
        $locations = Location::with('cabinets')->get();

        return Datatables::of($locations)
                        ->addColumn('action', function ($locations) {
                            return '<a onclick="editForm(' . $locations->id . ')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $locations->id . ')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
}
