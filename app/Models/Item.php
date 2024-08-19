<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $connection = 'cotte';
    protected $table = 'item';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'item_code', 'item_type', 'item_description', 'item_description_before', 'manufacture',
        'parameter_value', 'stock_group', 'sub_stock_group', 'uom', 'min_stock', 'max_stock',
        'lifetime', 'lifetime_uom', 'leadtime', 'part_no', 'specification', 'warehouse_location',
        'mr_max', 'item_parent', 'category', 'sub_category', 'next_category', 'item_category',
        'cogm_category', 'type_category', 'expense_acc', 'encumbrance_acc', 'buyer', 'reorder_qty',
        'packaging', 'pn', 'remark', 'ppn', 'coa_incoming', 'coa_outgoing', 'purpose', 'taxable',
        'is_stock', 'spb', 'pq', 'show_stock', 'used', 'po_5p', 'status', 'status_cotte', 'company',
        'trans_type', 'ditem_type', 'ditem_category', 'ditem_product', 'dcategory', 'variant',
        'sub_type', 'dimension', 'storage', 'reservation', 'item_model', 'findim', 'coverage',
        'product_group', 'created_date', 'created_by'
    ];

    public $timestamps = false;
}
