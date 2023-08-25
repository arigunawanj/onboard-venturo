<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesDetailModel extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    public $timestamps = true;
    protected $fillable = [
        't_sales_id',
        'm_product_id',
        'm_product_detail_id',
        'price',
        'discount_nominal',
    ];
    protected $table = 't_sales_detail';

   public function sales()
   {
       return $this->belongsTo(SalesModel::class, 't_sales_id', 'id');
   }

   public function product()
   {
       return $this->hasOne(ProductModel::class, 'id', 'm_product_id');
   }

}
