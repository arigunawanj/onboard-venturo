<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\RoleHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\User\RoleResource;
use App\Http\Resources\User\RoleCollection;

class RoleController extends Controller
{

    private $role;

    public function __construct()
    {
        $this->role = new RoleHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'access' => $request->access ?? '',
        ];
        $roles = $this->role->getAll($filter, 5, $request->sort ?? '');

        return response()->success(new RoleCollection($roles['data']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name','access']);
        $role = $this->role->create($payload);

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), "Role berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->role->getById($id);

        if (!($role['status'])) {
            return response()->failed(['Data role tidak ditemukan'], 404);
        }
        return response()->success(new RoleResource($role['data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['name', 'access', 'id']);
        $role = $this->role->update($payload, $payload['id'] ?? 0);

        if (!$role['status']) {
            return response()->failed($role['error']);
        }

        return response()->success(new RoleResource($role['data']), "Role berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->role->delete($id);

        if (!$role) {
            return response()->failed(['Mohon maaf data role tidak ditemukan']);
        }

        return response()->success($role, "Role berhasil dihapus");
    }
}
