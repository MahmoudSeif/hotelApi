<?php

namespace App\Http\Controllers\Api;
use App\Repository\HotelRepo;

use GuzzleHttp;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


class HotelController extends BaseController
{
    protected $hotelRepo;

    public function __construct(HotelRepo $hotelRepo)
    {
        $this->hotelRepo = $hotelRepo;
    }

    public function filter(Request $request)
    {
        if (!$request->all()) {
            return $this->response(100, 'No Parameters Found');
        } else {
            $guzzleClient = new GuzzleHttp\Client;
            $result = $guzzleClient->get('https://api.myjson.com/bins/pq0f6');
            $hotels = $result->getBody();
            $list = json_decode($hotels, true);
            $collection = new Collection($list['hotels']);
            $filter = $collection;

            $this->hotelRepo->setReq($request);

            $check = $this->hotelRepo->getAllhotels();
            if ($check == 0)
                return response()->json(['code' => 200, 'Hotels' => $filter]);
            else
            {
                $checkName = $this->hotelRepo->filterByName();
                if ($checkName['check'] == 1)
                {
                    $name = $checkName['success'];
                    $filter = $filter->where('name',$name);
                }
                $checkCity = $this->hotelRepo->filterByCity();
                if ($checkCity['check'] == 1)
                {
                    $city = $checkCity['success'];
                    $filter = $filter->where('city',$city);
                }
                $checkPriceRange = $this->hotelRepo->filterByPrice();
                if ($checkPriceRange['check'] == 0)
                {
                    return response()->json(['code' => 100, 'message' => 'You must Enter the Price Range']);
                }
                if ($checkPriceRange['check'] == 1)
                {
                    $priceFrom = $checkPriceRange['pFrom'];
                    $priceTo = $checkPriceRange['pTo'];
                    $filter = $filter->where('price',">=",$priceFrom)->where('price',"<=",$priceTo);
                }
                $checkDateRange = $this->hotelRepo->filterByDate();
                if ($checkDateRange['check'] == 0)
                {
                    return response()->json(['code' => 100, 'message' => 'You must Enter the Date Range']);
                }
                if ($checkDateRange['check'] == 1)
                {
                    $DateFrom = $checkDateRange['dFrom'];
                    $DateTo = $checkDateRange['dTo'];
                    $filter = $this->hotelRepo->filterByAvailability($filter,$DateFrom,$DateTo);
                }
            }
            return response()->json(['code' => 200, 'Hotels' => $filter]);
        }
    }

    public function sort(Request $request)
    {
        if (!$request->all()) {
            return $this->response(100, 'No Parameters Found');
        }
        else {
            $guzzleClient = new GuzzleHttp\Client;
            $result = $guzzleClient->get('https://api.myjson.com/bins/pq0f6');
            $hotels = $result->getBody();
            $list = json_decode($hotels, true);
            $collection = new Collection($list['hotels']);
            $type = $request->get('type');
            if ($type == 1) {
                $sort = $collection->sortBy('price')->values()->all();
            }
            elseif ($type == 2){
                $sort = $collection->sortBy('name')->values()->all();
            }
            else
            {
                $sort = $collection;
            }
            return response()->json(['code' => 200, 'Hotels' => $sort]);
        }
    }
}