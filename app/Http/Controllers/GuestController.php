<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SKU;
use App\Models\Zone;
use App\Models\Rack;

class GuestController extends Controller
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
                $query->where('sku_code', 'like', '%' . $search . '%');
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
        return view('dashboard', compact('zones', 'racks', 'skus'));
    }
}
