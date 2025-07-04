<?php

namespace App\Http\Controllers;
use App\Models\Users;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getCustomerByID(Request $request)
    {
        $response = (object) array();

        try {
            // Validate id จาก request body
            $validatedData = $request->validate([
                'id' => 'required|integer',
            ]);

            // ค้นหาลูกค้าด้วย id
            $users = Users::find($validatedData['id']);

            if ($users) {
                $response->message = 'Users found successfully';
                $response->status = 200;
                $response->data = (object)[
                    'id' => $users->id,
                    'userID' => $users->user_id,
                    'userName' => $users->user_name,
                    'email' => $users->email,
                ];
            } else {
                $response->message = 'Users not found';
                $response->status = 404;
                $response->data = (object)[];
            }
        } catch (\Exception $e) {
            $response->message = 'Failed to users.';
            $response->status = 500;
            $response->data = (object)[];
            $response->fetalMessage = $e->getMessage();
        }

        return response()->json($response)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }
}
