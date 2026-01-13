<?php

namespace App\Http\Controllers;

use App\Cabinet;
use App\Category;
use App\Drawer;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller {

    public function __construct() {
        $this->middleware('role:admin,staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $cabinets = Cabinet::with('items')->get();

        return view('cabinets.index', [
            'cabinets' => $cabinets,
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
                Rule::unique('cabinets')->where(function ($query) use ($request) {
                    return $query->where('location', $request->location);
                }),
            ],
            'location' => 'required|string|min:2'
        ]);

        $request['user_id'] = Auth::id();

        Cabinet::create($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Cabinet Created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $cabinet = Cabinet::with('items', 'drawers')->findOrFail($id);
        return view('cabinets.show')->compact('cabinet');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $cabinet = Cabinet::find($id);
        return $cabinet;
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
                        Rule::unique('cabinets')
                        ->where(function ($query) use ($request) {
                            return $query->where('location', $request->location);
                        })
                        ->ignore($id), // Ignore the current record
            ],
            'location' => 'required|string|min:2'
        ]);

        $cabinet = Cabinet::findOrFail($id);
        $cabinet->update($request->all());

        return response()->json([
                    'success' => true,
                    'message' => 'Cabinet Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $cabinet = Cabinet::findOrFail($id);

        // 1. Check for Drawers
        if ($cabinet->drawers()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This cabinet still contains ' . $cabinet->drawers()->count() . ' drawers. Please delete the drawers first.'
                            ], 422);
        }
        // 2. Check for Items (Directly linked to Cabinet)
        if ($cabinet->items()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This cabinet is currently linked to ' . $cabinet->items()->count() . ' items.'
                            ], 422);
        }
        // 3. If empty, proceed
        $cabinet->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Cabinet Deleted Successfully'
        ]);
    }

    public function storeDrawer(Request $request) {
        $this->validate($request, [
            'cabinet_id' => 'required|exists:cabinets,id',
            'title' => [
                'required',
                'string',
                'min:1',
                // Check uniqueness of title WHERE cabinet_id is the same as input
                Rule::unique('drawers')->where(function ($query) use ($request) {
                    return $query->where('cabinet_id', $request->cabinet_id);
                })->ignore($request->id), // Ignore current ID if we are editing
            ],
        ]);

        // Use updateOrCreate to handle both Add and Edit
        Drawer::updateOrCreate(
                ['id' => $request->id],
                [
                    'title' => $request->title,
                    'cabinet_id' => $request->cabinet_id
                ]
        );

        return response()->json([
                    'success' => true,
                    'message' => 'Drawer Saved Successfully'
        ]);
    }

    public function deleteDrawer($id) {
        $drawer = Drawer::findOrFail($id);

        // Safety Check: Prevent deletion if items are still inside this drawer
        if ($drawer->items()->count() > 0) {
            return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete! This drawer still contains ' . $drawer->items()->count() . ' items.'
                            ], 422);
        }

        $drawer->delete();

        return response()->json([
                    'success' => true,
                    'message' => 'Drawer Deleted'
        ]);
    }

    public function apiCabinets() {
        // Only fetch counts for the main list to keep it fast
        $cabinets = Cabinet::withCount(['items', 'drawers']);

        return Datatables::of($cabinets)
                        ->addColumn('action', function ($cabinet) {
                            return '<a onclick="editForm(' . $cabinet->id . ')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a> ' .
                                    '<a onclick="deleteData(' . $cabinet->id . ')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>';
                        })
                        ->make(true);
    }

// Fetch the full directory tree for one specific cabinet when expanded
    public function getCabinetDetails($id) {
        return Cabinet::with(['drawers.items'])->findOrFail($id);
    }
}
