<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class RolesPermissionController extends Controller
{
    public function index()
    {
        $data = [
            // 'roles' => Roles::all(),
            'li_active' => 'manajemen_roles',
        ];

        return view('superadmin/roles/index', $data);
    }

    public function getData(Request $request)
    {
        $query = Roles::select('*');
        $data = $query->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('encrypt_id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required|string|max:255',
            ],
            [
                'nama.required' => 'Nama Role harus diisi.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $dataInsert = [
                'name' => $request->nama,
            ];

            if ($request->uuid) {
                // Update User
                $role = Roles::where('uuid', $request->uuid)->firstOrFail();
                $role->update($dataInsert);
            } else {
                $dataInsert['uuid'] = Str::uuid();
                $dataInsert['guard_name'] = 'web';
                // Insert User (New) dengan UUID
                $role = Roles::create($dataInsert);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Role berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // public function assignRole(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $role = Roles::where('uuid', $request->user_uuid)->firstOrFail();
    //         $getRoles = Roles::whereIn('id', $request->roles)->pluck('name')->toArray();
    //         $role->syncRoles($getRoles);
    //         DB::commit();
    //         return response()->json(['status'=>true, 'message' => 'Role User Berhasil di Update']);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['status'=>false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    //     }
    // }

    public function switch_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = Roles::where('uuid', $request->uuid)->firstOrFail();
            $role->is_active = $request->status == '1' ? 1 : 0;
            $role->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Status Role Berhasil Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $role = Roles::where('uuid', $request->uuid)->firstOrFail();
            $role->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Role Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
