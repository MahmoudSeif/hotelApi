<?php
/**
 * Created by PhpStorm.
 * User: Mahmoud
 * Date: 11/13/2018
 * Time: 12:21 PM
 */
namespace App\Repository;
use Illuminate\Http\Request;

class HotelRepo
{
    private $request;
    private $result = array();

    public function setReq(Request $request)
    {
        $this->request = $request;
    }

    public function getAllHotels()
    {
        $name = $this->request->name;
        $city = $this->request->city;
        $priceFrom = $this->request->price_from;
        $priceTo = $this->request->price_to;
        $DateFrom = $this->request->date_from;
        $DateTo = $this->request->date_to;

        if (empty($name) && empty($city) && empty($priceFrom) && empty($priceTo) && empty($DateFrom) && empty($DateTo))
            return 0;
        else
            return 1;
    }

    public function filterByName()
    {
        $name = $this->request->name;
        if (!empty($name))
        {
            $this->result = ['check' => 1, 'success' => $name];
        }
        else
        {
            $this->result = ['check' => 0];
        }
        return $this->result;
    }

    public function filterByCity()
    {
        $city = $this->request->city;
        if (!empty($city))
        {
            $this->result = ['check' => 1, 'success' => $city];
        }
        else
        {
            $this->result = ['check' => 0];
        }
        return $this->result;
    }

    public function filterByPrice()
    {
        $priceFrom = $this->request->price_from;
        $priceTo = $this->request->price_to;
        if ( (empty($priceFrom) && !empty($priceTo)) || (empty($priceTo) && !empty($priceFrom)))
        {
            $this->result = ['check' => 0];
        }
        elseif (!empty($priceFrom) && !empty($priceTo))
        {
            $this->result = ['check' => 1, 'pFrom' => $priceFrom , 'pTo' => $priceTo];
        }
        else
        {
            $this->result = ['check' => 2];
        }
        return $this->result;
    }

    public function filterByDate()
    {
        $DateFrom = $this->request->date_from;
        $DateTo = $this->request->date_to;
        if ((empty($DateFrom) && !empty($DateTo)) || (empty($DateTo) && !empty($DateFrom)))
        {
            $this->result = ['check' => 0];
        }
        elseif (!empty($DateFrom) && !empty($DateTo))
        {
            $this->result = ['check' => 1, 'dFrom' => $DateFrom , 'dTo' => $DateTo];
        }
        else
        {
            $this->result = ['check' => 2];
        }
        return $this->result;
    }

    public function filterByAvailability($filterationData,$from,$to)
    {
        $filtration = [];
        $range = [];
        $dateRageResult = [];
        foreach ($filterationData as $data){
            foreach ($data['availability'] as $check)
            {
                if($check['from'] >= $from &&  $check['to'] <= $to){

                    $filtration['name'] = $data['name'];
                    $filtration['city'] = $data['city'];
                    $range['from'] = $check['from'];
                    $range['to'] = $check['to'];
                    $filtration['availability'] = $range;

                    $dateRageResult[] = $filtration;
                }
            }
        }
        return $dateRageResult;
    }
}