<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagsRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function create(TagsRequest $request)  {
        $tag = Tag::create($request->all());
        return response()->json(['data' => $tag, 'status' => true], 201);
    }


    public function getAll()  {
        return response()->json(['data' => Tag::all(), 'status' => true], 201);
    }

    
    
}
