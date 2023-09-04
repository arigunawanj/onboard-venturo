<?php
namespace App\Helpers\Sale;
use Throwable;
use App\Helpers\Venturo;
use App\Models\SalesDetailModel;
use App\Models\SalesModel;

class SaleHelper extends Venturo{
    private $sales;
    private $salesDetail;

    public function __construct(){
        $this->sales = new SalesModel();
        $this->salesDetail = new SalesDetailModel();
    }

    public function getSaleTransaction(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $sales = $this->salesDetail->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $sales
        ];
    }

    public function getAll($startDate, $endDate, $customerId = [], $promoId = [])
    {
        $sales = $this->sales->getAll($startDate, $endDate, $customerId, $promoId);

        return [
            'status' => true,
            'data' => $sales
        ];
    }

    public function getById(string $id): array
    {
        $sales = $this->sales->getById($id);
        if (empty($sales)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $sales
        ];
    }

    public function create(array $payload): array
    {
        try {
            $this->beginTransaction();

            $sales = $this->sales->store($payload);

            $this->insertUpdateDetail($payload['details'] ?? [], $sales->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sales
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload): array
    {
        try {
            $this->beginTransaction();

            $this->sales->edit($payload, $payload['id']);

            $this->insertUpdateDetail($payload['details'] ?? [], $payload['id']);
            $this->deleteDetail($payload['details_deleted'] ?? []);

            $sales = $this->getById($payload['id']);
            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $sales['data']
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $salesId)
    {
        try {
            $this->beginTransaction();

            $this->sales->drop($salesId);

            if(isset($this->salesDetail)){
                $this->salesDetail->dropBysalesId($salesId);
            }

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $salesId
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function insertUpdateDetail(array $details, string $salesId)
    {
        if (empty($details)) {
            return false;
        }

        foreach ($details as $val) {
            // Insert
            if (isset($val['is_added']) && $val['is_added']) {
                $val['m_product_id'] = $val['product_id'];
                $val['m_product_detail_id'] = $val['product_detail_id'];
                $val['t_sales_id'] = $salesId;
                $this->salesDetail->store($val);
            }

            // Update
            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->salesDetail->edit($val, $val['id']);
            }
        }
    }

    private function deleteDetail(array $details){
        if (empty($details)) {
            return false;
        }
        foreach ($details as $val) {
            $this->salesDetail->drop($val['id']);
        }
    }

}
