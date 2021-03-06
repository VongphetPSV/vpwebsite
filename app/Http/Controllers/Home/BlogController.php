<?php

namespace App\Http\Controllers\Home;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
     public function AllBlog()
    {
        $blogs = Blog::latest()->get();
        return view('admin.blogs.blogs_all', compact('blogs'));
    } // End Method

   
    public function AddBlog()
    {
        $categories = BlogCategory::orderBy('blog_category','ASC')->get();
        return view('admin.blogs.blogs_add',compact('categories'));
    } // End Method

    public function StoreBlog(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required',
            'blog_title' => 'required',
            'blog_tags' => 'required',
            'blog_description' => 'required',
            'blog_image' => 'required',
        ], [
            'blog_category_id.required' => 'ກະລຸນາໃສ່ປະເພດ ບົດຄວາມ',
            'blog_title.required' => 'ກະລຸນາໃສ່ຫົວຂໍ້ບົດຄວາມ',
            'blog_tags.required' => 'ກະລຸນາໃສ່ໝວດໝູ່ບົດຄວາມ',
            'blog_description.required' => 'ກະລຸນາໃສ່ເນື້ອໃນບົດຄວາມ',
            'blog_image.required' => 'ກະລຸນາໃສ່ຮູບພາບ',
        ]);

        $image = $request->file('blog_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        Image::make($image)->resize(430, 327)->save('upload/blog/' . $name_gen);
        $save_url = 'upload/blog/' . $name_gen;

        Blog::insert([
            'blog_category_id' => $request->blog_category_id,
            'blog_title' => $request->blog_title,
            'blog_tags' => $request->blog_tags,
            'blog_description' => $request->blog_description,
            'blog_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Blog Inserted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.blog')->with($notification);

    } // End Method

    public function EditBlog($id)
    {
        $blogs = Blog::findOrFail($id);
        $categories = BlogCategory::orderBy('blog_category','ASC')->get();
        return view('admin.blogs.blogs_edit',compact('blogs','categories'));
    } // End Method

    public function UpdateBlog (Request $request,$id) {
    
        if ($request->file('blog_image')) {
            $image = $request->file('blog_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            Image::make($image)->resize(430, 327)->save('upload/blog/' . $name_gen);
            $save_url = 'upload/blog/' . $name_gen;

            Blog::findOrFail($id)->update([
            'blog_category_id' => $request->blog_category_id,
            'blog_title' => $request->blog_title,
            'blog_tags' => $request->blog_tags,
            'blog_description' => $request->blog_description,
            'blog_image' => $save_url,
            'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Updated Blog with Image Successfully',
                'alert-type' => 'success',
            );

            return redirect()->route('all.blog')->with($notification);

        } else {

            Blog::findOrFail($id)->update([
                'blog_category_id' => $request->blog_category_id,
                'blog_title' => $request->blog_title,
                'blog_tags' => $request->blog_tags,
                'blog_description' => $request->blog_description,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Updated Blog without Image Successfully',
                'alert-type' => 'success',
            );

            return redirect()->route('all.blog')->with($notification);
        } // End else

    } // End Method

     public function DeleteBlog($id)
    {
        $blog = Blog::findOrFail($id);
        $img = $blog->blog_image;
        unlink($img);

        Blog::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Blog Deleted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);

    } // End Method

    public function BlogDetails($id) {

        $allblogs = Blog::latest()->limit(5)->get();
        $blogs = Blog::findOrFail($id);
        $categories = BlogCategory::orderBy('blog_category','ASC')->get();
        return view('frontend.blog_details',compact('blogs','allblogs','categories'));
    } // End Method

    public function CategoryPost($id) {
        $blogpost = Blog::where('blog_category_id',$id)->orderBy('id', 'DESC')->get();
        $allblogs = Blog::latest()->limit(5)->get();
        $categories = BlogCategory::orderBy('blog_category','ASC')->get();
        $categoryname = BlogCategory::findOrFail($id);
        return view('frontend.cat_blog_details',compact('blogpost','allblogs','categories','categoryname'));

    } // End Method

    public function HomeBlog() {
        $categories = BlogCategory::orderBy('blog_category','ASC')->get();
        $allblogs = Blog::latest()->paginate(3);
        return view('frontend.blog',compact('allblogs','categories'));
    } // End Method


}
