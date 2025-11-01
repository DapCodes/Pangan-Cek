<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;           // <- untuk hashing password
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Halaman utama manajemen user.
     */
    public function index()
    {
        // return view ke blade yang tampilannya mirip contoh "Komoditas"
        return view('main.users.index');
    }

    /**
     * Sumber data untuk DataTables (server-side).
     */
    public function getData()
    {
        // Ambil kolom yang diperlukan untuk tabel
        $users = User::select(['id', 'name', 'email', 'role', 'created_at', 'updated_at']);

        return DataTables::of($users)
            ->addIndexColumn() // nomor urut
            ->editColumn('created_at', fn($row) => optional($row->created_at)->format('d-m-Y H:i'))
            ->editColumn('updated_at', fn($row) => optional($row->updated_at)->format('d-m-Y H:i'))
            ->addColumn('action', function ($row) {
                // tombol edit & delete
                return '
                    <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="'.$row->id.'">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$row->id.'">
                        <i class="bx bx-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        // validasi input user baru
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6|confirmed',  // butuh password_confirmation
            'role'     => 'required|string|in:ADMIN,OFFICIAL,USER',
        ]);

        // hash password sebelum simpan ke DB
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
        ]);
    }

    /**
     * Ambil data user untuk modal edit.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update data user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // validasi update; email harus unik kecuali milik user ini
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,'.$id,
            'role'  => 'required|string|in:ADMIN,OFFICIAL,USER',
            // password opsional saat edit
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // bila field password dikirim, hash dan ikut update
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // jangan timpa password lama jika tidak diisi
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
        ]);
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: '.$e->getMessage(),
            ], 500);
        }
    }
}
