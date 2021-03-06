<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Transformers\Admin\TagTransformer;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function allTags()
    {
        $tags = Tag::all();
        return $this->response->collection($tags, new TagTransformer);
    }

    public function storeTags(Request $request)
    {
        $this->validate($request, ['tags' => 'required|array']);

        $tags = $request->input('tags');

        if (Tag::whereIn('name', $tags)->exists()) {
            abort(422, '存在重复标签');
        }

        Tag::insert(collect($tags)->map(function ($tag) {
            return [
                'name' => $tag,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all());

        $tags = Tag::whereIn('name', $tags)->get();

        return $this->response->collection($tags, new TagTransformer);
    }
}