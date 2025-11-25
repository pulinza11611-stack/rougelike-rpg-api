<?php

namespace App\Http\Controllers;
use App\Models\Users;
use App\Models\Items;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function getItemsByUserID(Request $request)
    {
        $response = (object) [];

        try {
            // Validate input
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
            ]);

            // ค้นหาผู้ใช้งาน
            $user = Users::where('user_id', $validatedData['user_id'])->first();

            if ($user) {
                // ดึงรายการ items ทั้งหมดที่ user_id ตรงกัน
                $items = Items::where('user_id', $validatedData['user_id'])->get();

                if ($items->count() > 0) {
                    // เตรียมรูปแบบข้อมูลที่ตอบกลับ
                    $response->message = 'Items retrieved successfully';
                    $response->status = 200;
                    $response->data = $items->map(function ($item) {
                        return (object) [
                            'id' => $item->id,
                            'userID' => $item->	user_id,
                            'itemID' => $item-> item_id,
                            'createdAt' => $item->created_at,
                            'updatedAt' => $item->updated_at,
                        ];
                    });
                } else {
                    $response->message = 'No items found for this user';
                    $response->status = 404;
                    $response->data = [];
                }
            } else {
                $response->message = 'User not found';
                $response->status = 404;
                $response->data = [];
            }
        } catch (\Exception $e) {
            $response->message = 'Internal Server Error';
            $response->status = 500;
            $response->data = (object) [];
            $response->fetalMessage = $e->getMessage(); // จะตัดออกตอน production ก็ได้
        }

        return response()->json($response);
    }
}
