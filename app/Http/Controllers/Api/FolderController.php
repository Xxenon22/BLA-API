<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\File;
use App\Http\Resources\FolderResource;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Folder::with('children', 'files')->whereNull('parent_id');

        // Pencarian folder berdasarkan folder_name atau description
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(folder_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $folders = $query->get();
        return response()->json($folders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $folder = Folder::create($request->only('folder_name', 'description'));

        return response()->json($folder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $folder = Folder::with(['children', 'files'])->findOrFail($id);
        return new FolderResource($folder);
    }

    /**
     * Get children of a specific folder.
     */
    public function getChildren(Request $request, $id)
    {
        $folder = Folder::with('children')->find($id);

        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        $search = $request->input('search');

        $children = $folder->children()->when($search, function ($query) use ($search) {
            $query->whereRaw('LOWER(folder_name) LIKE ?', ['%' . strtolower($search) . '%'])
                ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
        })->get();

        return response()->json(['data' => $children], 200);
    }

    public function getFilesBySubFolders($folderId, $fileId)
    {
        // Cari folder utama berdasarkan ID
        $folder = Folder::with('files') // Memuat relasi file
            ->find($folderId);

        if (!$folder) {
            return response()->json(['message' => 'Folder not found'], 404);
        }

        // Cari subfolder dalam folder
        $subfolder = $folder->children()->find($fileId);

        if (!$subfolder) {
            return response()->json(['message' => 'Subfolder not found'], 404);
        }

        // Menyertakan file dalam subfolder
        $subfolderWithFiles = $subfolder->load('files');

        return response()->json([
            'data' => [
                'folder' => $folder,
                'subfolder' => $subfolderWithFiles,
            ]
        ]);
    }



    /**
     * Create a subfolder for a specific folder.
     */
    public function createSubFolder(Request $request, $id)
    {
        $parentFolder = Folder::findOrFail($id);

        $request->validate([
            'folder_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $childFolder = Folder::create([
            'folder_name' => $request->folder_name,
            'description' => $request->description,
            'parent_id' => $id,
        ]);

        return response()->json([
            'data' => $childFolder,
            'message' => 'Child folder created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $folder = Folder::findOrFail($id);

        $request->validate([
            'folder_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $folder->update($request->only(['folder_name', 'description']));

        return response()->json([
            'message' => 'Folder updated successfully!',
            'data' => new FolderResource($folder),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();

        return response()->json(['message' => 'Folder deleted successfully']);
    }
}
