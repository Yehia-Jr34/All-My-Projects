<?php

namespace App\Http\Controllers;

use App\Models\CertificationsImages;
use App\Models\ProfilePicture;
use App\Models\UserImage;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    public function storeCertificationImages(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the image
        $path = $request->file('image')->store('images/certifications', 'public'); // Store in storage/app/public/images

        // Save the path in the database
        $image = CertificationsImages::create([
            'doctor_id' => $request->user()->id,
            'path' => $path
        ]);

        return response()->json([
            'message' => 'Image added successfully!'
        ]);
    }

    public function storeProfilePicture(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the image
        $path = $request->file('image')->store('images/profiles', 'public'); // Store in storage/app/public/images

        // Save the path in the database
        $image = ProfilePicture::create([
            'doctor_id' => $request->user()->id,
            'path' => $path
        ]);

        return response()->json([
            'message' => 'Image added successfully!'
        ]);
    }

    public function storeUserImages(Request $request)
    {
        // Validate the request
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the image
        $path = $request->file('image')->store('storage/app/public/images/userImages', 'public'); // Store in storage/app/public/images

        // Save the path in the database
        $image = UserImage::create([
            'user_id' => $request->user()->id,
            'path' => $path
        ]);

        return response()->json([
            'message' => 'Image added successfully!'
        ]);
    }
}
