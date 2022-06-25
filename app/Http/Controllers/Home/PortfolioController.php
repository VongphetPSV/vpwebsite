<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

class PortfolioController extends Controller
{
    public function AllPortfolio()
    {
        $portfolio = Portfolio::latest()->get();
        return view('admin.portfolio.portfolio_all', compact('portfolio'));
    } // End Method

    public function AddPortfolio()
    {
        return view('admin.portfolio.portfolio_add');
    } // End Method

    public function StorePortfolio(Request $request)
    {
        $request->validate([
            'portfolio_name' => 'required',
            'portfolio_title' => 'required',
            'portfolio_description' => 'required',
            'portfolio_image' => 'required',
        ], [
            'portfolio_name.required' => 'ກະລຸນາໃສ່ຊື່ ພອດໂພລິໂອ',
            'portfolio_title.required' => 'ກະລຸນາໃສ່ຫົວຂໍ້',
            'portfolio_description.required' => 'ກະລຸນາໃສ່ເນື້ອໃນ',
            'portfolio_image.required' => 'ກະລຸນາໃສ່ຮູບພາບ',
        ]);

        $image = $request->file('portfolio_image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        Image::make($image)->resize(1020, 519)->save('upload/portfolio/' . $name_gen);
        $save_url = 'upload/portfolio/' . $name_gen;

        Portfolio::insert([
            'portfolio_name' => $request->portfolio_name,
            'portfolio_title' => $request->portfolio_title,
            'portfolio_description' => $request->portfolio_description,
            'portfolio_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Portfolio Inserted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.portfolio')->with($notification);

    } // End Method

    public function EditPortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('admin.portfolio.portfolio_edit', compact('portfolio'));
    } // End Method

    public function UpdatePortfolio(Request $request)
    {

        $portfolio_id = $request->id;
        if ($request->file('portfolio_image')) {
            $image = $request->file('portfolio_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            Image::make($image)->resize(1020, 519)->save('upload/portfolio/' . $name_gen);
            $save_url = 'upload/portfolio/' . $name_gen;

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'portfolio_image' => $save_url,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Updated Portfolio with Image Successfully',
                'alert-type' => 'success',
            );

            return redirect()->route('all.portfolio')->with($notification);

        } else {

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Updated Portfolio without Image Successfully',
                'alert-type' => 'success',
            );

            return redirect()->route('all.portfolio')->with($notification);
        } // End else

    } // End Method

    public function DeletePortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        $img = $portfolio->portfolio_image;
        unlink($img);

        Portfolio::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Portfolio Deleted Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);

    } // End Method

    public function PortfolioDetails($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('frontend.portfolio_details',compact('portfolio'));
    }

    public function HomePortfolio(){
        $portfolio = Portfolio::latest()->get();
        return view('frontend.portfolio',compact('portfolio'));
    }

}
