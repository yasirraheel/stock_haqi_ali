<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhotoCategoryController extends Controller
{
    /**
     * Display a listing of photo categories
     */
    public function index()
    {
        $page_title = "Photo Categories";
        $categories = PhotoCategory::orderBy('name', 'asc')->get();

        return view('admin.photos.categories.index', compact('page_title', 'categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:photo_categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PhotoCategory::create($request->only(['name', 'description', 'status']));

            return redirect()->route('admin.photo-categories.index')
                ->with('flash_message', 'Category created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(PhotoCategory $photoCategory)
    {
        try {
            // Check if category is being used by any photos
            $photosCount = $photoCategory->photos()->count();

            if ($photosCount > 0) {
                return redirect()->back()
                    ->with('error', "Cannot delete category '{$photoCategory->name}'. It is being used by {$photosCount} photo(s).");
            }

            $photoCategory->delete();

            return redirect()->route('admin.photo-categories.index')
                ->with('flash_message', 'Category deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}
