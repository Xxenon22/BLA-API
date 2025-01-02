<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Folder;
use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;


class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $folderId)
    {
        $query = File::where('folder_id', $folderId);

        // Apply search filter if present
        if ($request->has('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(product_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(contact_person) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(vendor) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(material_position) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(material_description) LIKE ?', ["%{$search}%"]);
            });
        }

        // Filter by material_type_id if provided
        if ($request->has('material_type_id')) {
            $query->where('material_type_id', $request->input('material_type_id'));
        }

        $fileDatas = $query->paginate(10);

        return response()->json($fileDatas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $folderId)
    {
        // Validate input
        $validatedData = $request->validate([
            'material_type_id' => 'required|exists:material_types,id',
            'type_id' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'material_position' => 'nullable|string|max:255',
            'material_description' => 'nullable|string',
            'website' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        try {
            // Process image upload if exists
            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image')->store('images', 'public');
            }

            // Attach the folder_id
            $validatedData['folder_id'] = $folderId;

            // Create the file
            $file = File::create($validatedData);

            return response()->json([
                'message' => 'File created successfully',
                'data' => new FileResource($file),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the file',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($folderId, $fileId)
    {
        \Log::info("Fetching file with ID: $fileId from folder with ID: $folderId");

        // Check if folder and file exist
        $folder = Folder::find($folderId);
        $file = File::where('folder_id', $folderId)->find($fileId);


        if (!$folder || !$file) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json(['data' => new FileResource($file)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $folderId, $fileId)
    {
        $file = File::where('folder_id', $folderId)->findOrFail($fileId);

        $validator = Validator::make($request->all(), [
            'material_type_id' => 'required|exists:material_types,id',
            'type_id' => 'required|string|max:100',
            'product_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'material_position' => 'required|string|max:255',
            'material_description' => 'nullable',
            'website' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors(),
            ], 422);
        }

        // Handle image update
        if ($request->hasFile('image')) {
            if ($file->image) {
                Storage::disk('public')->delete($file->image);
            }
            $path = $request->file('image')->store('images', 'public');
            $file->image = $path;
        }

        $file->update([
            'material_type_id' => $request->material_type_id,
            'type_id' => $request->type_id,
            'product_name' => $request->product_name,
            'contact_person' => $request->contact_person,
            'material_position' => $request->material_position,
            'material_description' => $request->material_description,
            'vendor' => $request->vendor,
            'website' => $request->website,
        ]);

        return response()->json([
            'message' => 'File updated successfully',
            'data' => new FileResource($file),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($folderId, $fileId)
    {
        $file = File::where('folder_id', $folderId)->findOrFail($fileId);

        // Delete associated image if exists
        if ($file->image) {
            Storage::disk('public')->delete($file->image);
        }

        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
        ], 200);
    }

    /**
     * Filter files by material type ID.
     */
    public function getFilesByMaterialType($materialTypeId)
    {
        // $files = File::where('material_type_id', $materialTypeId)->get();

        // return response()->json([
        //     'success' => true,
        //     'data' => $files
        // ]);

        try {
            // Query untuk mengambil file berdasarkan material_type_id
            $files = File::where('material_type_id', $materialTypeId)->get();

            // Jika tidak ada file
            if ($files->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files found for this Material Type.',
                ], 404);
            }

            // Jika ada file, kirimkan data
            return response()->json([
                'success' => true,
                'data' => $files,
            ]);
        } catch (\Exception $e) {
            // Penanganan error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching files.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function showDetailFileByMaterialType($fileId)
    {
        // $file = File::findOrFail($id); // Ambil file berdasarkan ID atau kembalikan 404 jika tidak ditemukan

        // return response()->json([
        //     'success' => true,
        //     'data' => $file
        // ], 200);

        $file = File::with('materialType')->find($fileId); // Mengambil file bersama materialType
        return response()->json([
            'data' => $file
        ]);
    }
}
