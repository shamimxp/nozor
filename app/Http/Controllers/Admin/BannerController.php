<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $banners = Banner::latest()->get();
            return DataTables::of($banners)
                ->addIndexColumn()
                ->addColumn('image_preview', function ($row) {
                    $url = $row->image ? asset(config('imagepath.banner') . $row->image) : asset('images/no-image.png');
                    return '<img src="' . $url . '" width="120" class="img-thumbnail" alt="">';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-primary">
                                <input type="checkbox" class="custom-control-input changeStatus" data-id="' . $row->id . '" id="status_' . $row->id . '" ' . $status . '>
                                <label class="custom-control-label" for="status_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editBanner"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteBanner"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['image_preview', 'status', 'action'])
                ->make(true);
        }
        return view('admin.banner.index');
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'title'    => 'required|string|max:255',
            'sub_title'=> 'nullable|string|max:255',
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status'   => 'required|in:0,1',
        ]);


         $banner = new Banner();
            $banner->title = $request->title;
            $banner->sub_title = $request->sub_title;
            $banner->status = $request->status;

            if ($request->hasFile('image')) {
                $filename = $banner->uploadOne($request->image, 2378, 807, config('imagepath.banner'));
                $banner->image = $filename;
            }

            $banner->save();

            return response()->json(['success' => 'Banner created successfully.']);
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image) {
            $banner->image_url = asset(config('imagepath.banner') . $banner->image);
        } else {
            $banner->image_url = asset('images/no-image.png');
        }
        return response()->json($banner);
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title'    => 'required|string|max:255',
            'sub_title'=> 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'status'   => 'required|in:0,1',
        ]);

        try {
            $banner->title = $request->title;
            $banner->sub_title = $request->sub_title;
            $banner->status = $request->status;

            if ($request->hasFile('image')) {
                if ($banner->image) {
                    $banner->deleteOne(config('imagepath.banner'), $banner->image);
                }
                $filename = $banner->uploadOne($request->image, 2378, 807, config('imagepath.banner'));
                $banner->image = $filename;
            }

            $banner->save();

            return response()->json(['success' => 'Banner updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified banner.
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            if ($banner->image) {
                $banner->deleteOne(config('imagepath.banner'), $banner->image);
            }
            $banner->delete();
            return response()->json(['success' => 'Banner deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    /**
     * Toggle status of the banner.
     */
    public function getStatus(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
