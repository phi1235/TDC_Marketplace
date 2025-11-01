<?php
namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingPickupController extends Controller
{
    public function list(Listing $listing) {
        return response()->json(
            $listing->pickupPoints()->where('is_active', true)->orderBy('name')->get()
        );
    }

    public function sync(Request $r, Listing $listing) {
        // Option: $this->authorize('update', $listing);
        $data = $r->validate([
            'pickup_point_ids' => 'array',
            'pickup_point_ids.*' => 'integer|exists:pickup_points,id'
        ]);
        $listing->pickupPoints()->sync($data['pickup_point_ids'] ?? []);
        return response()->json(['message' => 'updated']);
    }
}
