<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Drawing;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class DrawingController extends Controller
{
    public function index() 
    {
        return Drawing::all();
    }

    public function search(Request $request, $id) 
    {
        $drawing = Drawing::where('uuid', '=', $id)->first();

        $drawing_members_array = json_decode($drawing->members);
        

        if (Auth::user()) {
            $user = User::where('id', '=', Auth::user()->id)->first();

            if (!in_array($user->name, $drawing_members_array)) {
                
    
                array_push($drawing_members_array, $user->name);
                
                $user_drawings = json_decode($user->drawings);
                array_push($user_drawings, $drawing->uuid);
                
                $user->drawings = json_encode($user_drawings);
                $user->save();

                dd($user);

            }
        } else {

            $name = $request->query()['name'];
            
            if (!in_array($name, $drawing_members_array)) {
                array_push($drawing_members_array, $name);
            }   
        }

        $drawing->members = json_encode($drawing_members_array);
        $drawing->save();

        return view('drawing', [
            'title' => 'Drawing',
            'css' => 'drawing',
            'drawing' => $drawing,
        ]);

    }

    public function user($id) 
    {
        return Drawing::where('creator', '=', $id)->get();
    }

    public function create(Request $request) 
    {
        $uuid = Str::orderedUuid()->toString(); 
        
        $drawing;

        if (Auth::user()) {
            $user = User::where('id', '=', Auth::user()->id)->first();

            $drawing = Drawing::create([
                'uuid' => $uuid,
                'creator' => $user->id,
                'members' => json_encode([
                    $user->name,
                ]),
                'history' => json_encode([]),
            ]);

            $user_drawings_array = json_decode($user->drawings);
            array_push($user_drawings_array, $drawing->uuid);
            $user->drawings = json_encode($user_drawings_array);
            $user->save();
        } else {
            $creator = json_decode($request->getContent())->name;
            
            $drawing = Drawing::create([
                'uuid' => $uuid,
                'creator' => $creator,
                'members' => json_encode([
                    $creator,
                ]),
                'history' => json_encode([]),
            ]);
        }

        
        $response = [
            'drawing' => $drawing,
        ];

        return response($response, 201);
    }
}