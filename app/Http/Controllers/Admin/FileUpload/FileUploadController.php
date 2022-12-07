<?php

namespace App\Http\Controllers\Admin\FileUpload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    //
    const LOCATION_STORE = "/storage/app/public/files/shares/";

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function fileUpload(Request $request, $file, $location_store = "", $want_rename = false): array
    {
        $fileName = $want_rename ? $file->getClientOriginalName() : time() . '-' . $file->getClientOriginalName();
        $filePath = $file->storeAs(self::LOCATION_STORE . $location_store, $fileName, "public");
        return ['success' => true, 'messages' => ['filename' => $fileName, 'type' => $request->file('file')->getClientOriginalExtension(), 'path' => $filePath]];
    }
}
