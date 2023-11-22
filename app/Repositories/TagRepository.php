<?php

namespace App\Repositories;

use App\Models\Tag;

class TagRepository
{
    protected $tag;
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function create(array $data)
    {
        return $this->tag->create($data);
    }

    public function search($tag){
        $tag = $this->tag->where('name',$tag)->groupBy('post_id', 'id')->get();
        return $tag;
    }

}
