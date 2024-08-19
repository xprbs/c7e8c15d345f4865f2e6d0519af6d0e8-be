<?php

namespace App\Http\Controllers;

use App\Models\LogItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LogItemController extends Controller
{
    public function index(Request $request)
    {
        $idItem = $request->input('id_item');

        if ($idItem) {
            // Ambil data log berdasarkan id_item
            $logItems = LogItem::where('id_item', $idItem)->get();
        } else {
            // Ambil semua data log jika id_item tidak diberikan
            $logItems = LogItem::all();
        }

        return response()->json($logItems);
    }

    public function store(Request $request)
    {
        // Validate and create a new log item
        $request->validate([
            'id_item' => 'required|integer',
            'type' => 'required|string|max:30',
            'message' => 'required|string',
        ]);

        $logItem = LogItem::create([
            'id_item' => $request->id_item,
            'type' => $request->type,
            'created_at' => Carbon::now(),
            'created_by' => Auth::user()->name,
            'message' => $request->message
        ]);

        return response()->json($logItem, 201);
    }

    public function show(Request $request)
    {
        // Show a specific log item
        $id = $request->input('id');
        $logItem = LogItem::findOrFail($id);
        return response()->json($logItem);
    }

    public function update(Request $request)
    {
        // Validate and update the log item
        $request->validate([
            'id' => 'required|integer',
            'id_item' => 'sometimes|integer',
            'type' => 'sometimes|string|max:30',
            'message' => 'sometimes|string',
            'created_by' => 'sometimes|string|max:30',
        ]);

        $logItem = LogItem::findOrFail($request->input('id'));
        $logItem->update($request->all());
        return response()->json($logItem);
    }

    public function destroy(Request $request)
    {
        // Delete a log item
        $id = $request->input('id');
        $logItem = LogItem::findOrFail($id);
        $logItem->delete();
        return response()->json(null, 204);
    }
}
