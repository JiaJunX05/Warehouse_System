<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rack;

class RackController extends Controller
{
    public function index(Request $request) {
        // 获取所有rack数据
        $racks = Rack::all();

        // 处理 AJAX 请求
        if ($request->ajax()) {
            // 查询rack数据
            $query = Rack::query();

            // rack过滤
            if ($request->filled('rack_id')) {
                $query->where('id', $request->input('rack_id'));
            }

            // 分页参数
            $perPage = $request->input('length', 9);
            $page = $request->input('page', 1);

            // 获取分页数据
            $racks = $query->paginate($perPage, ['*'], 'page', $page);

            // 返回 DataTables 兼容的 JSON 响应
            return response()->json([
                'draw' => $request->input('draw'), // DataTables 绘制计数器
                'recordsTotal' => $racks->total(),
                'recordsFiltered' => $racks->total(),
                'data' => $racks->items(),
                'current_page' => $racks->currentPage(),
                'last_page' => $racks->lastPage(),
                'total' => $racks->total(),
            ]);
        }

        // 非 AJAX 请求，返回初始视图数据
        return view('rack.dashboard', compact('racks'));
    }

    public function showCreateForm() {
        return view('rack.create');
    }

    public function create(Request $request) {
        $request->validate([
            'rack_number' => 'required|string|max:255|unique:racks',
        ]);

        $racks = Rack::create([
            'rack_number' => $trquest->rack_number,
            // 'rack_number' =>  strtoupper($request->rack_number),
        ]);

        return redirect()->route('rack.list')->with('success', 'Rack created successfully');
    }

    public function showUpdateForm($id) {
        $racks = Rack::find($id);
        return view('rack.update', compact('racks'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'rack_number' => 'required|string|max:255|unique:racks,rack_number,' . $id,
        ]);

        $racks = Rack::find($id);
        $racks->rack_number = $request->rack_number;
        // $racks->rack_number = strtoupper($request->rack_number);
        $racks->save();

        return redirect()->route('rack.list')->with('success', 'Rack updated successfully');
    }

    public function destroy($id) {
        $racks = Rack::findOrFail($id);

        if ($racks->storacks()->exists()) {
            return redirect()->route('rack.list')->withErrors(['error' => 'Cannot delete this rack because storacks are still linked to it.']);
        }

        $racks->delete();

        return redirect()->route('rack.list')->with('success', 'Rack deleted successfully');
    }
}
