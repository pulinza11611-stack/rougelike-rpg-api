<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

abstract class Controller
{
    public function getCustomerByID(Request $request)
    {
        $response = (object)[];
        $response->data = (object)[];

        try {
            // Validate id จาก request body
            $validatedData = $request->validate([
                'id' => 'required|integer',
            ]);

            // ค้นหาลูกค้าด้วย id
            $customer = Customer::find($validatedData['id']);

            if ($customer) {
                $response->data = (object)[
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                ];
                $response->message = 'Customer found successfully';
                $response->status = 200;
            } else {
                $response->status = 404;
                $response->message = 'Customer not found';
            }
        } catch (\Exception $e) {
            $response->status = 500;
            $response->message = 'Failed to retrieve customer.';
            $response->fetalMessage = $e->getMessage();
        }

        return response()->json($response)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }
}
