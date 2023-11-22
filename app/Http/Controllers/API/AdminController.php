<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\EloquentPostRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{

    protected $postRepository;

    public function __construct(EloquentPostRepository $post)
    {
        $this->postRepository = $post;
    }

    /**
     * Display a listing of the resource.
     */

    //Hiển thị tất cả các post chưa được phê duyệt
    public function getAllPostNoAccess()
    {
        try {
            $post = $this->postRepository->AllPostNoAccess();
            return response()->json([
                'success' => true,
                'body' => $post
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    //Hiển thị tất cả các bài post đã được kiểm duyệt
    public function getAllPostAccess()
    {
        try {
            $post = $this->postRepository->AllPostAccess();
            return response()->json([
                'success' => true,
                'body' => $post
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    //Kiểm duyệt các bài post
public function PostReviewer(string $id){
    try {
        $post = $this->postRepository->PostReviewer($id);
        return response()->json([
            'success' => true,
            'message'=>'The post has been reviewed',
            'body' => $post
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], Response::HTTP_NOT_FOUND);
    }
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
