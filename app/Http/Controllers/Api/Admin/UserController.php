<?php
namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Penumpang;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        return User::with('penumpang')->latest()->paginate(10);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            if ($validated['role'] === 'penumpang' && isset($validated['no_hp'])) {
                $user->penumpang()->create(['no_hp' => $validated['no_hp']]);
            }
            return $user;
        });

        return response()->json(['message' => 'User berhasil dibuat.', 'data' => $user->load('penumpang')], 201);
    }

    public function show(User $user)
    {
        return $user->load('penumpang');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        return response()->json(['message' => 'User berhasil diupdate.', 'data' => $user]);
    }

    public function destroy(User $user)
    {
        // Tambahkan validasi agar tidak bisa hapus diri sendiri
        if ($user->id_user === Auth::user()->id) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 403);
        }


        $user->delete();
        return response()->json(null, 204);
    }
}
