<?php
namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $storages = Storage::all();
        return response()->json($storages);
    }

    public function show($id)
    {
        $storage = Storage::find($id);

        if (!$storage) {
            return response()->json(['message' => 'Storage not found'], 404);
        }

        return response()->json($storage);
    }
}
