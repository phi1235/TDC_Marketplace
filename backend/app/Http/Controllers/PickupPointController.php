<?php
namespace App\Http\Controllers;

use App\Models\PickupPoint;
use Illuminate\Http\Request;

class PickupPointController extends Controller
{
    // Public: list active, có search
    public function index(Request $r) {
        $q = PickupPoint::query()->where('is_active', true);
        if ($r->filled('search')) {
            $s = $r->get('search');
            $q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('address','like',"%$s%"));
        }
        if ($r->filled('campus')) $q->where('campus_code', $r->campus);
        return response()->json($q->orderBy('name')->paginate(20));
    }

    // Admin
    public function store(Request $r) {
        $data = $r->validate([
            'name'=>'required|string|max:255',
            'campus_code'=>'nullable|string|max:50',
            'address'=>'nullable|string|max:255',
            'lat'=>'nullable|numeric',
            'lng'=>'nullable|numeric',
            'opening_hours'=>'nullable|array',
            'is_active'=>'boolean'
        ]);
        $p = PickupPoint::create($data);
        return response()->json($p, 201);
    }

    public function update(Request $r, PickupPoint $point) {
        $data = $r->validate([
            'name'=>'sometimes|required|string|max:255',
            'campus_code'=>'nullable|string|max:50',
            'address'=>'nullable|string|max:255',
            'lat'=>'nullable|numeric',
            'lng'=>'nullable|numeric',
            'opening_hours'=>'nullable|array',
            'is_active'=>'boolean'
        ]);
        $point->update($data);
        return response()->json($point);
    }

    public function destroy(PickupPoint $point) {
        $point->delete();
        return response()->json(['message'=>'deleted']);
    }
}
