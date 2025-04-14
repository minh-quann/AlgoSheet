<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SubCategoryController extends Controller
{
    public function index(Request $request) {
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
                            ->latest('id')
                            ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $subCategories = $subCategories->paginate(10);

        return view('admin.sub_category.list', compact('subCategories'));
    }

    public function create() {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.sub_category.create', compact('categories'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->show = $request->show;
            $subCategory->category_id = $request->category;
            $subCategory->save();


            $request->session()->flash('success', 'Sub Category added successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category added successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($subCategoryID, Request $request) {
        $subCategory = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->leftjoin('categories', 'categories.id', 'sub_categories.category_id')
            ->find($subCategoryID);
        if (empty($subCategory)) {
            return redirect()->route('sub-categories.index');
        }
        $categories = Category::orderBy('name', 'asc')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;

        return view('admin.sub_category.edit', $data);
    }

    public function update($subCategoryID, Request $request) {
        $subCategory = SubCategory::find($subCategoryID);
        if (empty($subCategory)) {
            $request->session()->flash('error', 'Sub Category not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Sub Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->show = $request->show;
            $subCategory->category_id = $request->category;
            $subCategory->save();


            $request->session()->flash('success', 'Sub Category updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($subCategoryID, Request $request) {
        $subCategory = SubCategory::find($subCategoryID);
        if (empty($subCategory)) {
            $request->session()->flash('error', 'Sub Category not found');
            return response()->json([
                'status' => false,
                'message' => 'Sub Category not found'
            ]);
        }

        $subCategory->delete();

        $request->session()->flash('success', 'Sub Category deleted successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully'
        ]);
    }
}
