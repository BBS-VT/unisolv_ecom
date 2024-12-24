<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:51200',
        ]);


        // Store file in temp storage
        $file = $request->file('file');
        $filePath = $file->store('temp_uploads');

        return response()->json([
            'message' => 'File uploaded successfully',
            'filePath' => $filePath,
        ]);

    }

}
