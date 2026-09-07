<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $imagePath = 'uploads/categories/' . $imageName;
        }

        Category::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'description' => $request->description,
            'is_featured' => $request->has('is_featured'),
            'status' => $request->has('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $category->image;

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }

            $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $imagePath = 'uploads/categories/' . $imageName;
        }

        $category->update([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'description' => $request->description,
            'is_featured' => $request->has('is_featured'),
            'status' => $request->has('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return back()->with('error', 'You cannot delete a category that has subcategories.');
        }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}