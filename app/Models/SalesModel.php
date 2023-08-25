<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesModel extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    public $timestamps = true;
    protected $fillable = [
        'm_customer_id',
        'm_voucher_id',
        'voucher_nominal',
        'm_discount_id',
        'date',
    ];
    protected $table = 't_sales';

    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'id', 'm_customer_id');
    }

    public function detail()
    {
        return $this->hasMany(SalesDetailModel::class, 't_sales_id', 'id');
    }

    public function voucher()
    {
        return $this->hasOne(VoucherModel::class, 'id', 'm_voucher_id');
    }

    public function getSalesPromo($startDate, $endDate, $customer = [], $promo = [])
    {
        $sales = $this->query()->with(['voucher', 'customer', 'voucher.promo']);

        if (!empty($startDate) && !empty($endDate)) {
            $sales->whereRaw('date >= "' . $startDate . ' 00:00:01" and date <= "' . $endDate . ' 23:59:59"');
        }

        if (!empty($customer)) {
            $sales->whereIn('m_customer_id', $customer);
        }

        if (!empty($promo)) {
            $sales->where(function ($query) use ($promo) {
                $query->whereIn('m_voucher_id', $promo)
                    ->orWhereIn('m_discount_id', $promo);
            });
        }

        $sales->whereNotNull('m_voucher_id');

        return $sales->orderByDesc('date')->get();
    }
}
