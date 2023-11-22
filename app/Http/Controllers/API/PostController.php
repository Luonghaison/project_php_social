<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Repositories\EloquentPostRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $postRepository;
    protected $tagRepository;

    /**
     * @param $post
     */
    public function __construct(EloquentPostRepository $post, TagRepository $tag)
    {
        $this->postRepository = $post;
        $this->tagRepository = $tag;
    }


    /**
     * Display a listing of the resource.
     */
    //Hiển thị thông tin chi tiết của 1 bài post theo id
    public function index($id)
    {
        try {
            $post = $this->postRepository->info($id);
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



    //Hiển thị toàn bộ thông tin của các bài post của mình trn trang cá nhân
    public function getAllMyPost()
    {
        try {
            $post = $this->postRepository->infoMyPost();
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

    //Hiển thị toàn bộ thông tin các bài post trên trang chủ bao gồm các bài post của mình và bạn bè của mình
    public function getAllPost()
    {
        try {
            $post = $this->postRepository->infoMyPostsAndFriendsPosts();
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

    //Tìm kiếm trong body post những tag ở sau dấu #
    public function getTags($body)
    {
        preg_match_all('/#(\w+)/', $body, $matches);

        $tags = array();

        for ($i = 0; $i < count($matches[1]); $i++) {

            $tag = $matches[1][$i];

            array_push($tags, $tag);
        }
        return $tags;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->input('body');
        $post = $this->postRepository->create([
            'body' => $data,
            'user_id' => Auth::user()->id]);

        //Thêm dữ liệu vào bảng tag tương ứng với bài post
        $tags = $this->getTags($data);
        foreach ($tags as $tag) {
            $post->tags()->create([
                'name' => $tag
            ]);
        }

        //Thêm ảnh vào bảng ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move('C:/xampp/htdocs/demo/intern_laravel/project_mini/resources/img/posts/', $filename);

                $post->images()->create([
                    'path' => 'C:/xampp/htdocs/demo/intern_laravel/project_mini/resources/img/posts/' . $filename,
                    'user_id' => Auth::user()->id
                ]);
            }

            return response()->json([
                'success' => true
            ]);
        }
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
        $body = $request->input('body');
        $data = [
            'body'=>$body,
            approval_at=>null
        ]

        $post = $this->postRepository->searchById($id);
        if (!$post) {
            return \response()->json([
                'success' => false,
                'message' => 'The post not found',
            ], Response::HTTP_NOT_FOUND);
        } else {
            $postUpdate = $this->postRepository->update($id, $data);
            $tags = $this->getTags($body);
            foreach ($tags as $tag) {
                $post->tags()->create([
                    'name' => $tag
                ]);
            return \response()->json([
                'success' => true,
                'message' => 'The post has been successfully edited',
                'title' => $postUpdate
            ], Response::HTTP_OK);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = $this->postRepository->searchById($id);
        if (!$post) {
            return \response()->json([
                'success' => false,
                'message' => 'The post not found',
            ], Response::HTTP_NOT_FOUND);
        } else {
            $post->delete($id);
            return \response()->json([
                'success' => true,
                'message' => 'The post is deleted',
            ], Response::HTTP_OK);
        }
    }
}
