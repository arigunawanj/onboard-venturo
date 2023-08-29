<?php

namespace App\Models;

use App\Models\Customer;
use App\Http\Traits\Uuid;
use App\Models\DiskonModel;
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
        return $this->belongsTo(DiskonModel::class, 'm_discount_id');
    }

    public function details(){
        return $this->hasMany(SalesDetailModel::class, 't_sales_id', 'id');
    }

    public function getAll($startDate, $endDate, $customer = [], $promo = [])
    {
        $sales = $this->query()->with(['voucher', 'discount', 'customer', 'voucher.promo', 'discount.promo']);

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

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesPromo($startDate, $endDate, $customer = [], $promo = [])
    {

        $sales = $this->query()->with(['voucher', 'discount', 'customer', 'voucher.promo', 'discount.promo']);

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

        return $sales->orderByDesc('date')->get();
    }

    public function getSalesByCustomer($startDate, $endDate, $customer = [])
    {
        $sales = $this->query()->with([
            'customer',
            'details',
            'discount',
            'discount.promo'
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
