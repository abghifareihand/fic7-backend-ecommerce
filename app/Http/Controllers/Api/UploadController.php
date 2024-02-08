<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        if ($request->has('image')) {
            $image = $request->image;
            $nameFile = time(). '.'. $image->getClientOriginalExtension();
            $path = public_path('uploads/images');
            $image->move($path, $nameFile);

            return response()->json([
                'image_path' => 'uploads/images/'.$nameFile,
                'base_url' => url('/'),
                'image_path_url' => url('/uploads/images/'.$nameFile),
            ]);
        } else {
            return response()->json([
                'message' => 'Tidak ada gambar yang diunggah',
            ], 400);
        }

    }

    public function uploadMultipleImage(Request $request)
    {
        if ($request->has('image')) {
           $images = $request->image;
           foreach ($images as $key => $image) {
               $nameFile = time(). $key . '.'. $image->getClientOriginalExtension();
               $path = public_path('uploads/images');
               $image->move($path, $nameFile);


           }
           return response()->json([
                'status' => 'upload multiple successful',
                'base_url' => url('/'),
            ]);
        } else {
            return response()->json([
                'message' => 'Tidak ada gambar yang diunggah',
            ], 400);
        }

    }
}
