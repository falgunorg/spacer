<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller {

    public function __construct() {
        // Everyone (admin/staff) can view, but only admin can create/edit/delete
        $this->middleware('role:admin,staff')->only(['index', 'apiUsers']);
        $this->middleware('role:admin')->except(['index', 'apiUsers']);
    }

    public function index() {
        return view('user.index');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required'
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);

        User::create($data);

        return response()->json([
                    'success' => true,
                    'message' => 'User Created Successfully'
        ]);
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        return $user;
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        $data = $request->all();
        // Only update password if it's filled
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
                    'success' => true,
                    'message' => 'User Updated Successfully'
        ]);
    }

    public function destroy($id) {
        User::destroy($id);
        return response()->json([
                    'success' => true,
                    'message' => 'User Deleted'
        ]);
    }

    public function apiUsers() {
        $users = User::query();

        return Datatables::of($users)
                        ->addColumn('action', function ($user) {
                            // Only show Edit/Delete if the logged-in user is an Admin
                            if (auth()->user()->role == 'admin') {
                                return '<a onclick="editForm(' . $user->id . ')" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a> ' .
                                        '<a onclick="deleteData(' . $user->id . ')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</a>';
                            }
                            return '<span class="label label-default">No Action</span>';
                        })
                        ->rawColumns(['action'])->make(true);
    }
}
