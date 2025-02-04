<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShippingController extends Controller
{
    function shipping_post($origin, $destination, $weight, $courier)
    {
        $baseCost = 10000;
        $weightFactor = 1000;
        $calculatedCost = $baseCost + ($weight / 1000) * $weightFactor;
        $mockResponse = [
            'shipping' => [
                'results' => [
                    [
                        'origin' => $origin,
                        'destination' => $destination,
                        'costs' => [
                            [
                                'service' => 'Standard',
                                'description' => 'Standard Shipping',
                                'cost' => [
                                    [
                                        'value' => $calculatedCost,
                                        'etd' => '3-5 days',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return json_encode($mockResponse);
    }

    function shipping_get($data)
    {
        if ($data == 'province') {
            $mockProvinceData = [
                'shipping' => [
                    'results' => [
                        ['province_id' => 1, 'province' => 'Jakarta'],
                        ['province_id' => 2, 'province' => 'Bandung'],
                        ['province_id' => 3, 'province' => 'Surabaya']
                    ]
                ]
            ];
            return json_encode($mockProvinceData);
        } elseif (strpos($data, 'city') !== false) {
            $mockCityData = [
                'shipping' => [
                    'results' => [
                        ['city_id' => 1, 'city_name' => 'Central Jakarta'],
                        ['city_id' => 2, 'city_name' => 'North Jakarta'],
                        ['city_id' => 3, 'city_name' => 'South Jakarta']
                    ]
                ]
            ];
            return json_encode($mockCityData);
        }
        return null;
    }

    public function province()
    {
        $province = $this->shipping_get('province');
        $data = json_decode($province, true);
        header("Content-Type: application/json");
        echo json_encode($data['shipping']['results']);
    }

    public function city($province_id)
    {
        if (!empty($province_id)) {
            if (is_numeric($province_id)) {
                $city = $this->shipping_get('city?province=' . $province_id);
                $data = json_decode($city, true);
                echo json_encode($data['shipping']['results']);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }

    public function cost($origin, $destination, $quantity, $courier)
    {
        $weight = (int)$quantity * 300;
        $price = $this->shipping_post($origin, $destination, $weight, $courier);
        $data = json_decode($price, true);
        echo json_encode($data['shipping']["results"]);
    }
}
