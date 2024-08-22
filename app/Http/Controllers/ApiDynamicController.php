<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiDynamicController extends Controller
{
    protected $url;
    public function __construct()
    {
        $this->url = "http://apidev.samora.co.id/api/samora-srv2";
    }
    public function getToken()
    {
        $url = "{$this->url}/auth/login";
        $body = [
            "username" => 'samora-api',
            "password" => 'SamoraBer1'
        ];

        $response = Http::post($url, $body)->collect();
        return response()->json($response['access_token'], 200);
    }

    public function fetchUOM(Request $request)
    {
        $token = $request->query('token') ? $request->query('token') : $this->getToken();
        $url = "{$this->url}/dynamic/master-data/UnitOfMeasure";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'dataAreaId' => $request->query('dataAreaId'),
            'UnitSymbol' => $request->query('UnitSymbol'),
            'TranslatedDescription' => $request->query('TranslatedDescription'),
        ])->get($url)->collect();
        return $response;
    }

    public function fetchProductCategories(Request $request)
    {
        $token = $request->query('token') ? $request->query('token') : $this->getToken();
        $url = "{$this->url}/dynamic/master-data/ProductCategoriesGet";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            "ProductCategoriesGet" => $request->query('ProductCategoriesGet'),
            "dataAreaId" => $request->query('dataAreaId'),
            "UnitSymbol" => $request->query('UnitSymbol'),
            "TranslatedDescription" => $request->query('TranslatedDescription'),
        ])->get($url)->collect();
        return $response;
    }

    public function fetchFinancialDimension(Request $request)
    {
        $token = $request->query('token') ? $request->query('token') : $this->getToken();
        $url = "{$this->url}/dynamic/master-data/FinancialDimension";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            "dataAreaId" => $request->query('dataAreaId'),
            "fin_dim_code" => $request->query('fin_dim_code'),
            "fin_dim_desc" => $request->query('fin_dim_desc'),
        ])->get($url)->collect();
        return $response;
    }
}
