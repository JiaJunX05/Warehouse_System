<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index(Request $request) {
        // 获取所有区域数据
        $zones = Zone::all();

        // 处理 AJAX 请求
        if ($request->ajax()) {
            // 查询区域数据
            $query = Zone::query();

            // 区域过滤
            if ($request->filled('zone_id')) {
                $query->where('id', $request->input('zone_id'));
            }

            // 分页参数
            $perPage = $request->input('length', 9);
            $page = $request->input('page', 1);

            // 获取分页数据
            $zones = $query->paginate($perPage, ['*'], 'page', $page);

            // 返回 DataTables 兼容的 JSON 响应
            return response()->json([
                'draw' => $request->input('draw'), // DataTables 绘制计数器
                'recordsTotal' => $zones->total(),
                'recordsFiltered' => $zones->total(),
                'data' => $zones->items(),
                'current_page' => $zones->currentPage(),
                'last_page' => $zones->lastPage(),
                'total' => $zones->total(),
            ]);
        }

        // 非 AJAX 请求，返回初始视图数据
        return view('zone.dashboard', compact('zones'));
    }


    public function showCreateForm() {
        return view('zone.create');
    }

    public function create(Request $request) {
        $request->validate([
            'zone_name' => 'required|string|max:255|unique:zones',
        ]);

        $zones = Zone::create([
            'zone_name' => $request->zone_name,
            // 'zone_name' =>  strtoupper($request->zone_name),
        ]);

        return redirect()->route('zone.list')->with('success', 'Zone created successfully');
    }

    public function showUpdateForm($id) {
        $zones = Zone::find($id);
        return view('zone.update', compact('zones'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'zone_name' => 'required|string|max:255|unique:zones,zone_name,' . $id,
        ]);

        $zones = Zone::find($id);
        $zones->zone_name = $request->zone_name;
        // $zones->zone_name = strtoupper($request->zone_name);
        $zones->save();

        return redirect()->route('zone.list')->with('success', 'Zone updated successfully');
    }

    public function destroy($id) {
        $zones = Zone::findOrFail($id);

        if ($zones->storacks()->exists()) {
            return redirect()->route('zone.list')->withErrors(['error' => 'Cannot delete this zone because storacks are still linked to it.']);
        }

        $zones->delete();

        return redirect()->route('zone.list')->with('success', 'Zone deleted successfully');
    }
}
