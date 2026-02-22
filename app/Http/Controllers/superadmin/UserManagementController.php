<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\RefJabatan;
use App\Models\RefJenisDokumen;
use App\Models\RefLembagaUMS;
use App\Models\Roles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $data = [
            'li_active' => 'delegasi_user',
            'lembaga_ums' => RefLembagaUMS::all(),
            'jabatans' => RefJabatan::all(),
            'role' => session('current_role'),
            'username' => Auth::user()->username
        ];

        if ($data['role'] == 'admin') {
            $data['roles'] = Roles::whereNot('name', 'superadmin')->get();
        } else if ($data['role'] == 'superadmin') {
            $data['roles'] = Roles::all();
        }


        return view('superadmin/users/index', $data);
    }

    public function getData(Request $request)
    {
        $query = User::select(['id', 'name', 'username', 'email', 'jabatan', 'status_tempat', 'place_state', 'uuid'])->with(['roles:id,name', 'getJabatan']); // Optimasi agar hanya mengambil ID & Nama Role

        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan nomor urut otomatis
            ->addColumn('encrypt_id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->addColumn('roles', function ($row) {
                return $row->roles->pluck('name')->implode(', ');
            })
            ->addColumn('uuid_jabatan', function ($row) {
                return $row->getJabatan ? $row->getJabatan->uuid : '';
            })
            ->addColumn('lembaga', function ($row) {
                return $row->status_tempat;
            })
            ->addColumn('id_roles', function ($row) {
                return $row->roles->pluck('id');
            })
            ->addColumn('is_online', function ($row) {
                return $row->isOnline() ? '<span class="badge bg-success">Online</span>' : '<span class="badge bg-danger">Offline</span>';
            })
            ->rawColumns(['is_online'])
            ->make(true); // Jangan lupa make(true) untuk respons JSON
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users', 'username')->ignore($request->uuid, 'uuid'), // Abaikan UUID saat update
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($request->uuid, 'uuid'), // Abaikan UUID saat update
                ],
            ],
            [
                'nama.required' => 'Nama harus diisi.',
                'username.required' => 'Username harus diisi.',
                'username.regex' => 'Username tidak boleh mengandung spasi.',
                'username.unique' => 'Username sudah terdaftar.',
                'email.required' => 'Email harus diisi.',
                'jabatan.required' => 'Jabatan harus diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.regex' => 'Email tidak boleh mengandung spasi.',
                'email.unique' => 'Email sudah terdaftar.',
            ],
        );

        if (!$request->uuid) {
            $validator = Validator::make(
                $request->all(),
                [
                    'password' => 'required',
                    'username' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('users', 'username'),
                    ],
                    'email' => [
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('users', 'email'),
                    ],
                ],
                [
                    'password.required' => 'Password Harus diisi.',
                    'username.required' => 'Username harus diisi.',
                    'username.regex' => 'Username tidak boleh mengandung spasi.',
                    'username.unique' => 'Username sudah terdaftar.',
                    'email.email' => 'Format email tidak valid.',
                    'email.regex' => 'Email tidak boleh mengandung spasi.',
                    'email.unique' => 'Email sudah terdaftar.',
                ],
            );
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $lembaga = RefLembagaUMS::where('id_lmbg', $request->lembaga)->firstOrFail();
            $jabatan = RefJabatan::where('uuid', $request->jabatan)->first();
            if (!$jabatan) {
                return response()->json(['error' => 'Jabatan tidak ditemukan.'], 404);
            }
            if ($lembaga->place_state != null) {
                $place_state = $lembaga->place_state;
            } else {
                $place_state = $request->lembaga;
            }
            $dataInsert = [
                'name' => $request->nama,
                'uniid' => $request->username,
                'username' => $request->username,
                'email' => $request->email,
                'place_state' => $place_state,
                'status_tempat' => $lembaga->nama_lmbg,
                'jabatan' => $jabatan->nama_jabatan,
                'uuid_jabatan' => $jabatan->jabatan,
            ];

            if ($request->filled('password')) {
                $dataInsert['password'] = Hash::make($request->password);
            }

            $dataInsert['remember_token'] = Str::random(60);

            if ($request->uuid) {
                // Update User
                $user = User::where('uuid', $request->uuid)->firstOrFail();
                $user->update($dataInsert);
            } else {
                $dataInsert['uuid'] = Str::uuid(); // Menggunakan UUID sebagai ID
                $dataInsert['is_active'] = '1'; // Menggunakan UUID sebagai ID
                $dataInsert['email_verified_at'] = date('Y-m-d H:i:s'); // Menggunakan UUID sebagai ID
                // Insert User (New) dengan UUID
                $user = User::create($dataInsert);
                $user->syncRoles('user');
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'User berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function assignRole(Request $request)
    {
        DB::beginTransaction();
        try {
            if (empty($request->roles)) {
                return response()->json(['error' => 'Pilih minimal 1 role.'], 403);
            }

            $reqRole = $request->roles;
            $allRole = Roles::pluck('id')->toArray();

            foreach ($reqRole as $roleId) {
                if (!in_array($roleId, $allRole)) {
                    return response()->json([
                        'error' => 'Form tidak valid, silakan reload tab terbaru.'
                    ], 403);
                }
            }


            $user = User::where('uuid', $request->user_uuid)->firstOrFail();
            $getRoles = Roles::whereIn('id', $request->roles)->pluck('name')->toArray();


            $user->syncRoles($getRoles);

            if (in_array('superadmin', $getRoles)) {
                $user->status_user = 'superadmin';
            } elseif (in_array('admin', $getRoles)) {
                $user->status_user = 'admin';
            } elseif (in_array('verifikator', $getRoles)) {
                $user->status_user = 'verifikator';
            } elseif (in_array('user', $getRoles)) {
                $user->status_user = 'user';
            }

            $user->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Role User Berhasil di Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function switch_status(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!in_array($request->status, ['1', '0'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengubah status.'
                ], 200);
            }
            $user = User::where('uuid', $request->uuid)->firstOrFail();
            $user->is_active = $request->status == '1' ? 1 : 0;
            $user->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Status User Berhasil Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('uuid', $request->uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'User Berhasil di Hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
