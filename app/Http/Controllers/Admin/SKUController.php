<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SKU;
use App\Models\Storack;
use App\Models\Zone;
use App\Models\Rack;

class SKUController extends Controller
{
    public function index(Request $request) {
        // 获取所有区域和货架数据
        $zones = Zone::all();
        $racks = Rack::all();

        // 处理 AJAX 请求
        if ($request->ajax()) {
            $query = SKU::with(['zone', 'rack']); // 预加载关联关系

            // 搜索过滤
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where('sku_code', 'like', "%{$search}%");
            }

            // 区域过滤
            if ($request->filled('zone_id')) {
                $query->where('zone_id', $request->input('zone_id'));
            }

            // 货架过滤
            if ($request->filled('rack_id')) {
                $query->where('rack_id', $request->input('rack_id'));
            }

            // 分页参数
            $perPage = $request->input('length', 10);
            $page = $request->input('page', 1);

            // 获取分页数据
            $skus = $query->paginate($perPage, ['*'], 'page', $page);

            // 返回 DataTables 兼容的 JSON 响应
            return response()->json([
                'draw' => $request->input('draw'), // DataTables 绘制计数器
                'recordsTotal' => $skus->total(),
                'recordsFiltered' => $skus->total(),
                'data' => $skus->items(),
                'current_page' => $skus->currentPage(),
                'last_page' => $skus->lastPage(),
                'total' => $skus->total(),
            ]);
        }

        // 非 AJAX 请求，返回初始视图数据
        $skus = SKU::with(['zone', 'rack'])->get();
        return view('admin.dashboard', compact('zones', 'racks', 'skus'));
    }


    public function showCreateForm() {
        $zones = Zone::all();
        $storacks = Storack::with('rack')->get();
        return view('admin.create', compact('zones', 'storacks'));
    }


    public function create(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku_code' => 'required|string|max:255|unique:skus,sku_code',
            'zone_id' => 'required|exists:zones,id',
            'rack_id' => 'nullable|exists:racks,id',
        ]);

        if ($imageFile = $request->file('image')) {
            $imageName = time() . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('assets/images'), $imageName);
        }

        $skus = SKU::create([
            'image' => 'images/' . $imageName,
            'sku_code' => strtoupper($request->sku_code),
            'zone_id' => $request->zone_id,
            'rack_id' => $request->rack_id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'SKU created successfully');
    }

    public function showUpdateForm($id) {
        $skus = SKU::findOrFail($id);
        $zones = Zone::all();
        $storacks = Storack::with('rack')->get();
        return view('admin.update', compact('skus', 'zones', 'storacks'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'sku_code' => 'required|string|max:255|unique:skus,sku_code,' . $id,
            'zone_id' => 'nullable|exists:zones,id',
            'rack_id' => 'nullable|exists:racks,id',
        ]);

        $skus = SKU::findOrFail($id);

        // 处理图片上传
        if ($request->hasFile('image')) {
            // 删除旧的图片文件
            if ($request->image && file_exists(public_path('assets/' . $skus->image))) {
                unlink(public_path('assets/' . $skus->image));
            }

            // 上传新的图片
            $imageName = time() . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/images'), $imageName);
            $skus->image = 'images/' . $imageName;
        }

        $skus->sku_code = strtoupper($request->sku_code);
        $skus->zone_id = $request->zone_id;
        $skus->rack_id = $request->rack_id ?: null;
        $skus->save();

        return redirect()->route('admin.dashboard')->with('success', 'SKU updated successfully');
    }

    public function destroy($id) {
        $skus = SKU::findOrFail($id);

        $imagePath = public_path('assets/' . $skus->image);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $skus->delete();

        return redirect()->route('admin.dashboard')->with('success', 'SKU deleted successfully');
    }
}
