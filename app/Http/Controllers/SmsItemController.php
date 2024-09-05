<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LogItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmsItemController extends Controller
{
    public function index(Request $request)
    {

        $valuationMethod = Item::whereNotNull('parameter_value')->select('parameter_value')->groupBy('parameter_value')->get();
        $stockGroup = Item::whereNotNull('stock_group')->whereNotNull('sub_stock_group')->select('stock_group', 'sub_stock_group')
            ->when($request->query('stock_group'), function ($query) use ($request) {
                return $query->where('stock_group', $request->query('stock_group'));
            })->groupBy('stock_group')->get();
        return response()->json([
            'status' => 'ok',
            'data' => [
                'valuation_method' => $valuationMethod,
                'stock_group' => $stockGroup
            ]
        ]);
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $item = Item::create($request->all());
            $itemLog = LogItem::create([
                'id_item' => $item->id_item,
                'type' => 'SUBMIT',
                'message' => $item->item_description,
                'created_by' => $item->created_by
            ]);
            DB::commit();
            return response()->json([
                'status' => 'ok',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $data = Item::where('id_item', $id)->first();
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $item = Item::findOrFail($id);
            $item->update($request->all());
            $itemLog = LogItem::create([
                'id_item' => $item->id_item,
                'type' => "UPDATE",
                'message' => $item->item_description,
                'created_by' => $item->created_by
            ]);
            DB::commit();
            return response()->json([
                'status' => 'ok',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
