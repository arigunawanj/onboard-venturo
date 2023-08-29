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
        $customers = $this->customer->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new CustomerCollection($customers['data']));
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

        $payload = $request->only(['name', 'phone_number', 'email', 'photo']);
        $customer = $this->customer->create($payload);

        if (!$customer['status']) {
            return response()->failed($customer['error']);
        }

        return response()->success(new CustomerResource($customer['data']), "Customer berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customer->getById($id);

        if (!($customer['status'])) {
            return response()->failed(['Data customer tidak ditemukan'], 404);
        }
        return response()->success(new CustomerResource($customer['data']));
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

        $payload = $request->only(['name', 'id', 'email', 'phone_number', 'photo']);
        $customer = $this->customer->update($payload, $payload['id'] ?? 0);

        if (!$customer['status']) {
            return response()->failed($customer['error']);
        }

       

        return response()->success(new CustomerResource($customer['data']), "Customer berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = $this->customer->delete($id);

        if (!$customer) {
            return response()->failed(['Mohon maaf data customer tidak ditemukan']);
        }

        return response()->success($customer, "Customer berhasil dihapus");
    }
}
