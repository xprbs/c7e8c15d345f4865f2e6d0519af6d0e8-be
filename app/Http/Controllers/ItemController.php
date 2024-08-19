<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('limit', 10);
        $currentPage = $request->get('page', 1);
        $company = $request->get('company');
        $filterData = $request->get('filterData', '');

        // Default companies if no company parameter is provided
        $defaultCompanies = ['SUJD', 'AFD', 'MSID'];

        // Query to filter items by company if provided, otherwise use default companies
        $query = Item::when($request->query('type') && $request->query('type') == 'fico', function ($query) {
            return $query->where(function ($query) {
                return $query->where('status_cotte', 1)->whereIn('status', [1, 2, 3]);
            });
        });

        if ($company) {
            $query->where('company', $company);
        } else {
            $query->whereIn('company', $defaultCompanies);
        }

        // Add filter conditions for each relevant column
        $query->where(function ($query) use ($filterData) {
            $query->where('trans_type', 'like', '%' . $filterData . '%')
                ->orWhere('item_code', 'like', '%' . $filterData . '%')
                ->orWhere('item_description', 'like', '%' . $filterData . '%')
                ->orWhere('uom', 'like', '%' . $filterData . '%')
                ->orWhere('status_cotte', 'like', '%' . $filterData . '%')
                ->orWhere('status', 'like', '%' . $filterData . '%');
        });

        $items = $query->when($request->query('type') && $request->query('type') == 'fico', function ($query) {
            return $query->orderBy('status', 'DESC');
        })->when(!$request->query('type'), function ($query) {
            return $query->orderBy('status_cotte', 'DESC');
        })->paginate($perPage);

        // Map status_cotte and status values to their corresponding labels
        $items->getCollection()->transform(function ($item) {
            $item->status_cotte = $this->mapStatus($item->status_cotte);
            $item->status = $this->mapStatus($item->status);

            // Map company AFD to ANF
            if ($item->company === 'AFD') {
                $item->company = 'ANF';
            }

            return $item;
        });

        return response()->json([
            'code' => 200,
            'currentPage' => $currentPage,
            'data' => $items->items(),
            'firstItem' => $items->firstItem(),
            'lastItem' => $items->lastItem(),
            'lastPage' => $items->lastPage(),
            'message' => 'Successfully get data',
            'nextPageUrl' => $items->nextPageUrl(),
            'perPage' => $items->perPage(),
            'previousPageUrl' => $items->previousPageUrl(),
            'total' => $items->total(),
        ]);
    }

    private function mapStatus($value)
    {
        $mapping = [
            1 => 'Approve',
            2 => 'Reject',
            3 => 'Need Approve'
        ];

        return $mapping[$value] ?? $value;
    }

    public function store(Request $request)
    {
        $item = Item::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return response()->json($item);
    }

    public function updateregitrasi(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        foreach ($request->all() as $key => $value) {
            if ($value !== null) {
                if ($key === 'type') {
                    $item->ditem_type = $value;
                } else {
                    $item->$key = $value;
                }
            }
        }

        $item->save();

        return response()->json($item, 200);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        Log::info('Receiving request');
        try {
            $item = Item::findOrFail($id);
            $item->update($request->all());
            $token = $this->getToken();
            Log::info('Hit first api');
            $hitFirst = $this->productCreations('SUJ', $item, $token);
            Log::info('Hit first api done');

            Log::info('Hit second api');
            $hitSecond = $this->idSumRelease('SUJ', $item, $token);
            Log::info('Hit second api done');



            // if (isset($hitFirst['response'])) {
            //     $response = $hitFirst->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }

            // if (isset($hitSecond['response'])) {
            //     $response = $hitSecond->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }


            return response()->json([
                'status' => 'success',
                'message' => 'All request successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateanf(Request $request, $id)
    {
        DB::beginTransaction();
        Log::info('Receiving request');
        try {
            $item = Item::findOrFail($id);
            $item->update($request->all());
            $token = $this->getToken();
            Log::info('Hit first api');
            $hitFirst = $this->productCreations('ANF', $item, $token);
            Log::info('Hit first api done');

            Log::info('Hit second api');
            $hitSecond = $this->idSumRelease('ANF', $item, $token);
            Log::info('Hit second api done');



            // if (isset($hitFirst['response'])) {
            //     $response = $hitFirst->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }

            // if (isset($hitSecond['response'])) {
            //     $response = $hitSecond->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }


            return response()->json([
                'status' => 'success',
                'message' => 'All request successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updatemsi(Request $request, $id)
    {
        DB::beginTransaction();
        Log::info('Receiving request');
        try {
            $item = Item::findOrFail($id);
            $item->update($request->all());
            $token = $this->getToken();
            Log::info('Hit first api');
            $hitFirst = $this->productCreations('MSI', $item, $token);
            Log::info('Hit first api done');

            Log::info('Hit second api');
            $hitSecond = $this->idSumRelease('MSI', $item, $token);
            Log::info('Hit second api done');



            // if (isset($hitFirst['response'])) {
            //     $response = $hitFirst->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }

            // if (isset($hitSecond['response'])) {
            //     $response = $hitSecond->collect();
            //     throw new \Exception($response['response']['error']['innererror']['message']);
            // }


            return response()->json([
                'status' => 'success',
                'message' => 'All request successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return response()->json(null, 204);
    }

    public function getToken()
    {
        $url = "http://apidev.samora.co.id/api/samora-srv2/auth/login";
        $body = [
            "username" => 'samora-api',
            "password" => 'SamoraBer1'
        ];

        $response = Http::post($url, $body)->collect();
        return $response['access_token'];
    }

    public function productCreations($areaid, $item, $token)
    {
        $url = 'http://apidev.samora.co.id/api/samora-srv2/dynamic/master-data/ReleasedProductCreationsV2';

        $body = [
            "dataAreaId" => strtolower($areaid),
            "ItemNumber" => $item->item_code,
            "ProductNumber" => $item->item_code,
            "ProductType" => $item->item_type,
            "ProductSubType" => $item->sub_type,
            "ProductDimensionGroupName" => $item->dimension,
            "SearchName" => $item->item_description,
            "ProductDescription" => $item->item_description,
            "ProductSearchName" => $item->item_description,
            "ProductName" => $item->item_description
        ];

        $body = json_encode($body);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->withBody($body, 'application/json')->post($url);
        Log::info('Response body first api', [$response->collect()]);

        return $response;
    }

    public function idSumRelease($areaid, $item, $token)
    {
        $url = 'http://apidev.samora.co.id/api/samora-srv2/dynamic/master-data/IID_SUM_ReleasedProductsV2';

        $body = [
            "dataAreaId" => strtolower($areaid),
            "ItemNumber" => $item->item_code,
            "ProductNumber" => $item->item_code,
            "ProductGroupId" => $item->product_group,
            "InventoryReservationHierarchyName" => $item->reservation,
            "StorageDimensionGroupName" => $item->storage,
            "TrackingDimensionGroupName" => $item->reservation,
            "ItemModelGroupId" => $item->item_model,
            "DefaultLedgerDimensionDisplayValue" => $item->findim,
            "ProductCoverageGroupId" => "",
            "ProductCategoryName" => $item->ditem_category,
            "ProductCategoryHierarchyName" => "Products Category",
            "IID_SUM_UOM" => $item->uom,
            "ProductDimensionGroupName" => $item->dimension
        ];

        // Log::info('request body second api', $body);
        $body = json_encode($body);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->withBody($body, 'application/json')->post($url);
        // Log::info('Response body second api', [$response->collect()]);

        return $response;
    }
}
