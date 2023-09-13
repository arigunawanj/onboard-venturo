<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Models\Customer;
use App\Models\Promo;
use App\Models\VoucherModel;
use App\Models\SalesDetailModel;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesModel extends Model implements CrudInterface
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $table = 't_sales';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'm_customer_id',
        'm_voucher_id',
        'voucher_nominal',
        'm_discount_id',
        'date',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'm_customer_id');
    }

    public function voucher(){
        return $this->belongsTo(VoucherModel::class, 'm_voucher_id');
    }

    public function discount(){
        return $this->belongsTo(Promo::class, 'm_discount_id');
    }

    public function details(){
        return $this->hasMany(SalesDetailModel::class, 't_sales_id', 'id');
    }

    public function getAll($startDate, $endDate, $customer = [], $promo = [])
    {
        $sales = $this->query()->with(['voucher', 'discount', 'customer', 'voucher.promo']);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }
        if (!empty($promo)) {
            $sales->where(function ($query) use ($promo) {
                $query->whereIn('m_voucher_id',function ($query) use ($promo) {
                    $query->select('m_voucher.id')->from('m_voucher')->whereIn('m_voucher.m_promo_id',$promo);
                // })->orWhereIn('m_discount_id',function ($query) use ($promo) {
                //     $query->select('m_discount.id')->from('m_discount')->whereIn('m_discount.m_promo_id',$promo);
                });
            });
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesPromo($startDate, $endDate, $customer = [], $promo = [])
    {

        $sales = $this->query()->with(['voucher', 'discount', 'customer', 'voucher.promo']);


        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }


        if (!empty($promo)) {
            $sales->where(function ($query) use ($promo) {
                $query->whereIn('m_voucher_id',function ($query) use ($promo) {
                    $query->select('m_voucher.id')->from('m_voucher')->whereIn('m_voucher.m_promo_id',$promo);
                })->orWhereIn('m_discount_id',function ($query) use ($promo) {
                    $query->select('m_discount.id')->from('m_discount')->whereIn('m_discount.m_promo_id',$promo);
                });
            });
        }

        $sales->whereNotNull('m_voucher_id')->orWhereNotNull('m_discount_id');

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesByCategory($startDate, $endDate, $category = '')
    {
        $sales = $this->query()->with([
            'details.product' => function ($query) use ($category) {
                if (!empty($category)) {
                    $query->where('m_product_category_id', $category);
                }
            },
            'details',
            'details.product.category'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        return $sales->orderByDesc('date')->get(); //basic report
        // return $sales->orderByDesc('date')->limit(2)->get();

    }

    public function getSalesByCustomer($startDate, $endDate, $customer = [])
    {
        $sales = $this->query()->with([
            'customer',
            'details.product',
            'discount',
            'voucher.promo'
        ]);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        return $sales->orderByDesc('date')->get();
    }

    public function getByIdCustomer($date, int $id){
        $sales = $this->query()->with([
            'customer',
            'details',
            'discount',
            'discount.promo'
        ]);

        if(!empty($date)){
            $sales->whereRaw('date >= "' . $date . ' 00:00:01" and date <= "' . $date . ' 23:59:59"');
        }

        return $sales->where('m_customer_id', $id)->get();
    }

     public function getSaleTransaction(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $sale = $this->query()->with([
            'details',
            'voucher', 'discount', 'customer', 'voucher.promo'
        ]);

        if (!empty($filter['m_product_id']) && is_array($filter['m_product_id'])) {
            $productIds= $filter['m_product_id'];
            $sale->where(function ($query) use ($productIds) {
                $query->whereIn('t_sales_detail_product_id',function ($query) use ($productIds) {
                    $query->select('t_sales_detail.m_product_id')->from('t_sales_detail')->whereIn('t_sales_detail.m_product_id',$productIds);
                });
            });
        }
        if (!empty($filter['m_customer_id']) && is_array($filter['m_customer_id'])) {
            $customerId = $filter['m_customer_id'];

            $sale->whereIn('m_customer_id',$customerId);
        }
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $startDate = $filter['start_date'];
            $endDate = $filter['end_date'];
            $sale->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }
        $sort = $sort ?: 'id DESC';
        $sale->orderByRaw($sort);
        // dd($sale->get());
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $sale->paginate($itemPerPage)->appends('sort', $sort);
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
