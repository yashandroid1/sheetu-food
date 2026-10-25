<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Carbon\Carbon;
use CURLFile;

class GalleryController extends Controller
{
    private function getCloudinaryConfig()
    {
        return [
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'daizpwrnu'),
            'api_key'    => env('CLOUDINARY_API_KEY', '386253942553317'),
            'api_secret' => env('CLOUDINARY_API_SECRET', 'QXXpDJ9___om1oGHh54JXemfEs4'),
        ];
    }

    private function uploadToCloudinary($file)
    {
        $config = $this->getCloudinaryConfig();
        $timestamp = time();
        $unique_id = time() . "_" . rand(1000, 9999);

        $signature_string = "folder=FoodGalllery&public_id={$unique_id}&timestamp={$timestamp}" . $config['api_secret'];
        $signature = sha1($signature_string);

        $url = "https://api.cloudinary.com/v1_1/" . $config['cloud_name'] . "/image/upload";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);

        $filePath = $file->getRealPath();
        $mimeType = $file->getMimeType();
        $fileName = $file->getClientOriginalName();

        $data = [
            "file"      => new CURLFile($filePath, $mimeType, $fileName),
            "api_key"   => $config['api_key'],
            "timestamp" => $timestamp,
            "signature" => $signature,
            "folder"    => "FoodGalllery",
            "public_id" => $unique_id,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['error' => $err];
        }

        return json_decode($response, true);
    }

    public function index()
    {
        $items = Gallery::orderBy('created_at', 'desc')->get();
        return view('admin.gallery.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $uploadResult = $this->uploadToCloudinary($request->file('image'));

        if (!empty($uploadResult['secure_url'])) {
            Gallery::create([
                'title'      => trim($request->input('title')),
                'image'      => $uploadResult['secure_url'],
                'created_at' => Carbon::now(),
            ]);

            return redirect()->route('admin.gallery.index')->with('flash', [
                'type' => 'success',
                'msg'  => 'Gallery item added successfully!'
            ]);
        }

        $errorMsg = $uploadResult['error'] ?? 'Failed to upload image to Cloudinary.';
        return redirect()->back()->withInput()->with('flash', [
            'type' => 'danger',
            'msg'  => $errorMsg
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'gallery_id' => 'required|integer',
            'title'      => 'required|string|max:255',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $item = Gallery::findOrFail($request->input('gallery_id'));
        $item->title = trim($request->input('title'));

        if ($request->hasFile('image')) {
            $uploadResult = $this->uploadToCloudinary($request->file('image'));
            if (!empty($uploadResult['secure_url'])) {
                $item->image = $uploadResult['secure_url'];
            } else {
                return redirect()->back()->with('flash', [
                    'type' => 'danger',
                    'msg'  => 'Failed to upload new image!'
                ]);
            }
        }

        $item->save();

        return redirect()->route('admin.gallery.index')->with('flash', [
            'type' => 'success',
            'msg'  => 'Gallery item updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $item = Gallery::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.gallery.index')->with('flash', [
            'type' => 'success',
            'msg'  => 'Gallery item deleted successfully!'
        ]);
    }
}
