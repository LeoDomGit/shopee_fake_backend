<?php

namespace App\Http\Controllers;

use App\Models\Shops;
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ShopsController extends Controller
{
    protected $aws_secret_key;
    protected $aws_access_key;

    public function __construct()
    {
        $this->aws_secret_key = 'b52dcdbea046cc2cc13a5b767a1c71ea8acbe96422b3e45525d3678ce2b5ed3e';
        $this->aws_access_key = 'cbb3e2fea7c7f3e7af09b67eeec7d62c';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    private function uploadToCloudFlareFromFile($file, $folder)
    {
        try {
            // Step 1: Prepare Cloudflare R2 credentials and settings
            $accountid = '453d5dc9390394015b582d09c1e82365';
            $r2bucket = 'artapp';  // Updated bucket name
            $accessKey = $this->aws_access_key;
            $secretKey = $this->aws_secret_key;
            $region = 'auto';
            $endpoint = "https://$accountid.r2.cloudflarestorage.com";

            // Set up the S3 client with Cloudflare's endpoint
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => $accessKey,
                    'secret' => $secretKey,
                ],
                'endpoint' => $endpoint,
                'use_path_style_endpoint' => true,
            ]);

            // Step 2: Define the object path and name in R2
            $finalFileName = time().'.png';
            $r2object = $folder . '/' . $finalFileName;

            // Step 3: Upload the file to Cloudflare R2
            try {
                $result = $s3Client->putObject([
                    'Bucket' => $r2bucket,
                    'Key' => $r2object,
                    'Body' => file_get_contents($file->getRealPath()), // Get the file content
                    'ContentType' => $file->getMimeType(),
                ]);

                // Generate the CDN URL using the custom domain
                $cdnUrl = "https://artapp.promptme.info/$folder/$finalFileName";
                return $cdnUrl;
            } catch (S3Exception $e) {
                Log::error("Error uploading file: " . $e->getMessage());
                return 'error: ' . $e->getMessage();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return 'error: ' . $th->getMessage();
        }
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
            'name' => 'required',
            'phone'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }
        $check=Shops::where('seller_id',Auth::id())->first();
        if($check){
            return response()->json(['check'=>false,'msg'=>'Đã tạo shop','data'=>$check]);
        }
        $data=$request->all();
        $data['seller_id']=Auth::id();
        $data['created_at']=now();
        Shops::create($data);
        $data=Shops::where('seller_id',Auth::id())->first();
        return response()->json(['check'=>true,'data'=>$data]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shops $shops)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shops $shops)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shops $shops,$id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'seller_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['check' => false, 'msg' => $validator->errors()->first()]);
        }
        $shop = Shops::findOrFail($id);
        $data=$request->all();
        $data['seller_id']=Auth::id();
        $data['updated_at']=now();
        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder='store';
            $cloudflareLink = $this->uploadToCloudFlareFromFile($file, $folder);
            $data['image']=$cloudflareLink;
        }
        $shop->update($data);
        $shop = Shops::findOrFail($id);
        return response()->json([
            'check' => true,
            'data' =>$shop
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shops $shops)
    {
        //
    }
}
