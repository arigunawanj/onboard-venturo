<?php

namespace App\Http\Controllers\api;

use App\Helpers\User\CustomerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\CreateCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Http\Resources\Customer\CustomerCollection;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    private $customer;

    public function __construct()
    {
        $this->customer = new CustomerHelper();
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
            'email' => $request->email ?? '',
        ];
        $users = $this->customer->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new CustomerCollection($users['data']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'phone_number', 'user_roles_id']);
        $user = $this->customer->create($payload);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new CustomerResource($user['data']), "User berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->customer->getById($id);

        if (!($user['status'])) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }
        return response()->success(new CustomerResource($user['data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['email', 'name', 'password', 'photo', 'phone_number', 'user_roles_id']);
        $user = $this->customer->update($payload, $payload['id'] ?? 0);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new CustomerResource($user['data']), "User berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->customer->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user, "User berhasil dihapus");
    }
}
