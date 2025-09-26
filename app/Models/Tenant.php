<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table ="tenant_category_transaction_view";
    protected $primaryKey = 'id';
    protected $fillable = [
     'idParentCategory', 'sumSubtotal', 'sumQuantity'
 ];
}
