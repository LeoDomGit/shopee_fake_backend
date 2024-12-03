<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Shops;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth ;

class CategoriesController extends Controller
{
    // Create a new Category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }

        $data = $request->all();
        $userId = Auth::id();
        $shopId = Shops::where('seller_id', $userId)->value('id');

        if (!$shopId) {
            return response()->json(['check' => false, 'msg' => 'Shop not found for the seller.']);
        }

        $data['shop_id'] = $shopId;
        $data['slug'] = Str::slug($request->name);
        $data['created_at'] = now();

        // Create the category
        $category = Categories::create($data);

        // Handle tags
        $tags = collect($request->tags)->map(function ($tagName) {
            // Check if the tag exists by name
            $tag = Tag::firstOrCreate(['name' => $tagName], [
                'slug' => Str::slug($tagName),
            ]);

            // Return the tag instance
            return $tag;
        });

        // Attach tags to the category
        $category->tags()->sync($tags->pluck('id')->toArray());

        // Fetch updated category data with tags
        $category = Categories::with('tags')->where('shop_id', $shopId)->get();

        return response()->json(['check' => true, 'data' => $category]);
    }

    // Update an existing Category
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|unique:categories,name,' . $id,
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }

        $category = Categories::findOrFail($id);

        if ($request->has('name')) {
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
        }

        // Handle tags update
        if ($request->has('tags')) {
            $tags = collect($request->tags)->map(function ($tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName], [
                    'slug' => Str::slug($tagName),
                ]);
                return $tag;
            });

            // Sync tags (this will remove old tags and add new ones)
            $category->tags()->sync($tags->pluck('id')->toArray());
        }

        $category->save();

        // Fetch updated category data with tags
        $category = Categories::with('tags')->find($id);

        return response()->json(['check' => true, 'data' => $category]);
    }

    // Delete a Category
    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->tags()->detach();
        $category->delete();
        return response()->json(['check' => true, 'msg' => 'Category deleted successfully.']);
    }
}
