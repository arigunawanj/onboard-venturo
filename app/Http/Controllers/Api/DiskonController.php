<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Diskon\DiskonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Diskon\DiskonRequest;
use App\Http\Resources\Diskon\DiskonCollection;
use App\Http\Resources\Diskon\DiskonResource;
use Illuminate\Http\Request;

class DiskonController extends Controller
{

    private $diskon;

    public function __construct()
    {
        $this->diskon = new DiskonHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'm_customer_id' => isset($request->customer_id) ? explode(',', $request->customer_id) : [],
        ];

        $diskon = $this->diskon->getAll($filter, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success(new DiskonCollection($diskon['data']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiskonRequest $request)
    {
         /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['customer_id', 'promo_id', 'is_status']);
        $payload = $this->renamePayload($payload);
        $diskon = $this->diskon->create($payload);

        if (!$diskon['status']) {
            return response()->failed($diskon['error']);
        }

        return response()->success(new DiskonResource($diskon['data']), "Diskon berhasil ditambahkan");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $diskon = $this->diskon->getById($id);

        if (!($diskon['status'])) {
            return response()->failed(['Data diskon tidak ditemukan'], 404);
        }
        return response()->success(new DiskonResource($diskon['data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DiskonRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/UpdateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['customer_id', 'id', 'promo_id', 'is_status']);
        $payload = $this->renamePayload($payload);
        $diskon = $this->diskon->update($payload, $payload['id'] ?? 0);

        if (!$diskon['status']) {
            return response()->failed($diskon['error']);
        }

        return response()->success(new DiskonResource($diskon['data']), "Diskon berhasil diubah");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $diskon = $this->diskon->delete($id);

        if (!$diskon) {
            return response()->failed(['Mohon maaf data Diskon tidak ditemukan']);
        }

        return response()->success($diskon, "Diskon berhasil dihapus");
    }

    public function renamePayload($payload) {
        $payload['m_customer_id'] = $payload['customer_id'] ?? null;
        $payload['m_promo_id'] = $payload['promo_id'] ?? null;
        unset($payload['customer_id']);
        unset($payload['promo_id']);
        return $payload;
    }
}
