<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function PHPUnit\Framework\stringContains;

class TagController extends Controller
{
    protected $tagRepository;

    /**
     * @param $tag
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }


    public function search($tag)
    {
        $tag = $this->tagRepository->search($tag);
        if ($tag->isEmpty()) {
            return \response()->json([
                'success' => false,
                'message' => 'No results found'
            ], Response::HTTP_NOT_FOUND);
        }

        return \response()->json([
            'success' => true,
            'tag' => $tag
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
