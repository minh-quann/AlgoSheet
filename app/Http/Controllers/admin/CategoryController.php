<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class CategoryController extends Controller
{
    public function index(Request $request) {
        $categories = Category::latest();

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(10);

        return view('admin.category.list', compact('categories'));
    }

    public function create() {
        return view('admin.category.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories'
        ]);

        if ($validator->passes()) {

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->show = $request->show;
            $category->save();

            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath, $dPath);

                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $manager = new ImageManager(new Driver());
                $img = $manager->read($sPath);
                $img->scale(450, 600);
                $img->save($dPath);
                $category->image = $newImageName;
                $category->save();

                File::delete($sPath);
            }

            $request->session()->flash('success', 'Category added successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category added successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($categoryID, Request $request) {

        $category = Category::find($categoryID);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit', compact('category'));
    }

    public function update($categoryID, Request $request) {
        $category = Category::find($categoryID);
        if (empty($category)) {
            $request->session()->flash('error', 'Category not found');

            return response()->json([
               'status' => false,
               'notFound' => true,
               'message' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);

        if ($validator->passes()) {

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->show = $request->show;
            $category->save();

            $oldImage = $category->image;


            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'-'.time().'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath, $dPath);

                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $manager = new ImageManager(new Driver());
                $img = $manager->read($sPath);
                $img->scale(450, 600);
                $img->save($dPath);
                $category->image = $newImageName;
                $category->save();

                File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);
            }

            $request->session()->flash('success', 'Category updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function destroy($categoryID, Request $request) {
        $category = Category::find($categoryID);
        if (empty($category)) {
            $request->session()->flash('error', 'Category not found');
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ]);
        }


        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        $request->session()->flash('success', 'Category deleted successfully.');

        return response()->json([
           'status' => true,
           'message' => 'Category deleted successfully'
        ]);
    }
}
