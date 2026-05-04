<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return response()->json(Branch::all());
    }

    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        $user = $request->user();

        // Security: Check if user is allowed in this branch
        if ($request->branch_id && $user->email !== 'rebin.maaruf@gmail.com') {
             $isAllowed = $user->branches()->where('branches.id', $request->branch_id)->exists();
             if (!$isAllowed) {
                 return response()->json(['error' => 'You are not authorized for this branch'], 403);
             }
        }

        $user->branch_id = $request->branch_id;
        $user->save();

        return response()->json([
            'message' => 'Branch switched successfully',
            'branch' => Branch::find($request->branch_id)
        ]);
    }
}
