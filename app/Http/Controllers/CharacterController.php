<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Users;

use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function getCharacterByUserID(Request $request)
    {
        $response = (object) [];

        try {
            $validatedData = $request->validate([
                'user_id' => 'required|integer',
                'character_id' => 'required', // เป็น VARCHAR ได้ ไม่ต้อง integer
            ]);

            // ดึง user
            $user = Users::where('user_id', $validatedData['user_id'])->first();

            // ดึง character ตาม user_id + character_id
            $character = Character::where('user_id', $validatedData['user_id'])
                ->where('character_id', $validatedData['character_id'])
                ->first();

            if ($user && $character) {
                $response->message = 'User and Character found';
                $response->status = 200;
                $response->data = (object)[
                    'user' => (object)[
                        'id' => $user->id,
                        'userID' => $user->user_id,
                        'userName' => $user->user_name,
                        'email' => $user->email,
                        'createdAt' => $user->created_at,
                        'updatedAt' => $user->updated_at,
                    ],
                    'character' => (object)[
                        'id' => $character->id,
                        'characterID' => $character->character_id,
                        'characterName' => $character->character_name,
                        'class' => $character->class,
                        'level' => $character->level,
                        'exp' => $character->exp,
                        'createdAt' => $character->created_at,
                        'updatedAt' => $character->updated_at,
                    ]
                ];
            } else {
                $response->message = 'User or Character not found';
                $response->status = 404;
                $response->data = (object)[];
            }
        } catch (\Exception $e) {
            $response->message = 'Internal Server Error';
            $response->status = 500;
            $response->data = (object)[];
            $response->fetalMessage = $e->getMessage();
        }

        return response()->json($response);
    }


    public function createCharacter(Request $request)
    {
        $response = (object) [];

        try {
            // Validate input
            $validatedData = $request->validate([
                'user_id' => 'required',
                'character_name' => 'required|string',
                'class' => 'required|string',
            ]);

            // ตรวจสอบว่า user มีอยู่หรือไม่
            $user = Users::where('user_id', $validatedData['user_id'])->first();

            if (!$user) {
                $response->message = 'User not found';
                $response->status = 404;
                $response->data = (object)[];
                return response()->json($response);
            }

            // ดึง character ทั้งหมดของ user
            $characters = Character::where('user_id', $validatedData['user_id'])->get();

            $characterCount = $characters->count();

            if ($characterCount >= 4) {
                $response->message = 'Character limit reached (maximum 4 per user)';
                $response->status = 403;
                $response->data = (object)[];
                return response()->json($response);
            }

            // ดึง character_id ทั้งหมดที่ user มี
            $existingIDs = $characters->pluck('character_id')->map(function ($id) {
                return intval($id); // กรณี character_id เป็น string
            })->toArray();

            // หาเลข 1-4 ที่ยังไม่มี แล้วเอาตัวที่เล็กสุด
            $allPossibleIDs = [1, 2, 3, 4];
            $availableIDs = array_diff($allPossibleIDs, $existingIDs);

            if (empty($availableIDs)) {
                $response->message = 'No available character ID';
                $response->status = 409;
                $response->data = (object)[];
                return response()->json($response);
            }

            $newCharacterID = min($availableIDs);

            // สร้าง character ใหม่
            $character = Character::create([
                'character_id' => $newCharacterID,
                'user_id' => $validatedData['user_id'],
                'character_name' => $validatedData['character_name'],
                'class' => $validatedData['class'],
                'level' => 0,
                'exp' => 0,
            ]);

            $response->message = 'Character created successfully';
            $response->status = 201;
            $response->data = (object)[
                'characterID' => $character->character_id,
                'characterName' => $character->character_name,
                'class' => $character->class,
                'level' => $character->level,
                'exp' => $character->exp,
                'createdAt' => $character->created_at,
            ];
        } catch (\Exception $e) {
            $response->message = 'Failed to create character';
            $response->status = 500;
            $response->data = (object)[];
            $response->fetalMessage = $e->getMessage();
        }

        return response()->json($response);
    }
}
