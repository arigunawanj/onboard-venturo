<?php

namespace App\Models;

use App\Models\Promo;
use App\Models\Customer;
use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherModel extends Model implements CrudInterface
{
    use HasFactory;
    use SoftDeletes; // Use SoftDeletes library
    use Uuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'm_customer_id',
        'm_promo_id',
        'start_time',
        'end_time',
        'total_voucher',
        'nominal_rupiah',
        'photo',
        'description'
    ];
    protected $table = 'm_voucher';

    protected $casts = [
        'id' => 'string',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'm_customer_id');
    }

    public function promo()
    {
        return $this->hasOne(Promo::class, 'id', 'm_promo_id');
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['m_customer_id']) && is_array($filter['m_customer_id'])) {
            $user->whereIn('m_customer_id', $filter['m_customer_id']);
        }

        if (!empty($filter['start_time']) && !empty($filter['end_time'])) {
            $dateVoucherBetweenParam = '((start_time >= "' . $filter['start_time'] . '" and start_time <= "' . $filter['end_time'] . '") or (end_time >= "' . $filter['start_time'] . '" and end_time <= "' . $filter['end_time'] . '")';


            $dateParamBetweenVoucher = '("' . $filter['start_time'] . '" >= start_time and "' . $filter['end_time'] . '" <= end_time) or ("' . $filter['end_time'] . '" >= start_time and "' . $filter['end_time'] . '" <= end_time))';


            $user->whereRaw('
                (' . $dateVoucherBetweenParam . ') or
                (' . $dateParamBetweenVoucher . ')
            ');
        }

            $sort = $sort ?: 'id DESC';
            $user->orderByRaw($sort);
            $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

            return $user->paginate($itemPerPage)->appends('sort', $sort);
        }

    public function getById(string $id)
    {
        return $this->find($id);
    }

}
