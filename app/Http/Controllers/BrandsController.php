<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Shops;
use App\Models\Tag;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name',
            'tags' => 'required|array',
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

        // Create the brand
        $brand = Brands::create($data);

        // Handle tags
        $tags = collect($request->tags)->map(function ($tagName) {
            // Check if the tag exists by name
            $tag = Tag::firstOrCreate(['name' => $tagName], [
                'slug' => Str::slug($tagName),
            ]);

            // Return the tag instance
            return $tag;
        });

        // Attach tags to the brand
        $brand->tags()->sync($tags->pluck('id')->toArray());

        // Fetch updated brand data with tags
        $data = Brands::with('tags')->where('shop_id', $shopId)->get();

        return response()->json(['check' => true, 'data' => $data]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brands $brands)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brands $brands)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brands $brands,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|unique:brands,name,' . $id, // Allow updating name if it's unique, but skip validation for the current brand.
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }

        // Find the brand by ID
        $brand = Brands::find($id);

        if (!$brand) {
            return response()->json(['check' => false, 'msg' => 'Brand not found.']);
        }

        $userId = Auth::id();
        $shopId = Shops::where('seller_id', $userId)->value('id');

        if (!$shopId) {
            return response()->json(['check' => false, 'msg' => 'Shop not found for the seller.']);
        }

        // Check if the brand belongs to the correct shop
        if ($brand->shop_id != $shopId) {
            return response()->json(['check' => false, 'msg' => 'You do not have permission to update this brand.']);
        }

        // Update name and slug if a new name is provided
        if ($request->has('name')) {
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->name);
        }

        // Update other fields as necessary
        $brand->updated_at = now();
        $data=$request->all();
        $data['updated_at'] = now();
        $brand->update($data);
        // Handle tags if provided
        if ($request->has('tags')) {
            // Create new tags or find existing ones
            $tags = collect($request->tags)->map(function ($tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName], [
                    'slug' => Str::slug($tagName),
                ]);
                return $tag;
            });

            // Sync new tags with the brand (removes old ones and adds new)
            $brand->tags()->sync($tags->pluck('id')->toArray());
        }

        // Fetch updated brand data with tags
        $data = Brands::with('tags')->where('shop_id', $shopId)->get();

        return response()->json(['check' => true, 'data' => $data]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brands $brands)
    {
        //
    }
}
