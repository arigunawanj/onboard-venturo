<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Models\SalesModel;
use App\Models\ProductModel;
use App\Repository\CrudInterface;
use App\Models\ProductDetailModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesDetailModel extends Model implements CrudInterface
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    public $timestamps=true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'total_item',
        'price',
        'discount_nominal'
    ];
    protected $table = 't_sales_detail';

    public function sales(){
        return $this->belongsTo(SalesModel::class, 't_sales_id');
    }

    public function product(){
        return $this->hasOne(ProductModel::class, 'id', 'm_product_id');
    }

    public function productDetail(){
        return $this->hasOne(ProductDetailModel::class, 'id', 'm_product_detail_id');
    }

    public function getTotalSaleByPeriode(string $startDate, string $endDate) :int
    {
        $total = $this->query()
            ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_sales'))
            ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                        $query->whereRaw('date >= "' . $startDate . ' 00:00:01"
                                            and date <= "' . $endDate . ' 23:59:59"');
                    })
            ->first()
            ->toArray();

        return $total['total_sales'] ?? 0;
    }

    public function getListYear()
    {
        $sales   = new SalesModel();
        $years   = $sales->query()
                    ->select(DB::raw('Distinct(year(date)) as year'))
                    ->get()
                    ->toArray();

        return array_map(function ($year) {
            return $year['year'];
        }, $years);
    }

    public function getTotalPerYears(string $year): int{
        $totalPerYear = $this->query()
        ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_per_year'))
        ->whereHas('sales', function ($query) use ($year) {
            $query->whereRaw('year(date) = "'. $year .'"');
        })
        ->first()
        ->toArray();

        return $totalPerYear['total_per_year'] ?? 0;
    }

    public function getListMonth()
    {
        $sales   = new SalesModel();
        $months   = $sales->query()
                    ->select(DB::raw('Distinct(month(date)) as month'))
                    ->get()
                    ->toArray();

        return array_map(function ($month) {
            return $month['month'];
        }, $months);
    }

    public function getTotalPerMonths(string $month, string $year): int{
        $totalPerMonth = $this->query()
        ->select(DB::raw('sum((total_item * price) - discount_nominal) as total_per_month'))
        ->whereHas('sales', function ($query) use ($month, $year) {
            $query->whereRaw('month(date) = "'. $month .'" and year(date) = "'. $year.'"');
        })
        ->first()
        ->toArray();

        return $totalPerMonth['total_per_month'] ?? 0;
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $saleDetail = $this->query();

        if (!empty($filter['m_product_id']) && is_array($filter['m_product_id'])) {
            $saleDetail->whereIn('m_product_id', $filter['m_product_id']);
        }
        if (!empty($filter['m_customer_id']) && is_array($filter['m_customer_id'])) {
            $customerId = $filter['m_customer_id'];

            $saleDetail->where(function ($query) use ($customerId) {
                $query->whereIn('t_sales_id',function ($query) use ($customerId) {
                    $query->select('t_sales.id')->from('t_sales')->whereIn('t_sales.m_customer_id',$customerId);
                });
            });
        }
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $startDate = $filter['start_date'];
            $endDate = $filter['end_date'];
            $saleDetail->where(function ($query) use ($startDate, $endDate) {
                $query->whereIn('t_sales_id',function ($query) use ($startDate, $endDate) {
                    $query->select('t_sales.id')->from('t_sales')
                    ->whereRaw('t_sales.date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
                });
            });
        }
        $sort = $sort ?: 'id DESC';
        $saleDetail->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $saleDetail->paginate($itemPerPage)->appends('sort', $sort);
    }

     public function getAllExport(array $filter)
    {
        $saleDetail = $this->query();

        if (!empty($filter['m_product_id']) && is_array($filter['m_product_id'])) {
            $saleDetail->whereIn('m_product_id', $filter['m_product_id']);
        }
        if (!empty($filter['m_customer_id']) && is_array($filter['m_customer_id'])) {
            $customerId = $filter['m_customer_id'];

            $saleDetail->where(function ($query) use ($customerId) {
                $query->whereIn('t_sales_id',function ($query) use ($customerId) {
                    $query->select('t_sales.id')->from('t_sales')->whereIn('t_sales.m_customer_id',$customerId);
                });
            });
        }
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $startDate = $filter['start_date'];
            $endDate = $filter['end_date'];
            $saleDetail->where(function ($query) use ($startDate, $endDate) {
                $query->whereIn('t_sales_id',function ($query) use ($startDate, $endDate) {
                    $query->select('t_sales.id')->from('t_sales')
                    ->whereRaw('t_sales.date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
                });
            });
        }
        $saleDetail->orderByRaw('id DESC');


        return $saleDetail->get();
    }

    public function getById(string $id)
    {
        return $this->find($id);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }




}
