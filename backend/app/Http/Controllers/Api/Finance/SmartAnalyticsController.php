<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SmartAnalyticsService;

class SmartAnalyticsController extends Controller
{
    /**
     * Fetch predictions and anomalies for the requested branch scope.
     */
    public static function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = false;
        
        try {
            $isSuperAdmin = $user->hasRole('Super Admin');
        } catch (\Throwable $e) {}

        $hasPermission = false;
        try {
            $hasPermission = $user->hasPermissionTo('verify_database_integrity') || $user->hasPermissionTo('view_advanced_reports');
        } catch (\Throwable $e) {}

        if (!$isSuperAdmin && !$hasPermission && $user->email !== 'rebin.maaruf@gmail.com') {
            return response()->json([
                'message' => 'مۆڵەتی پێویستت نییە بۆ بینینی شیکاری زیرەکی دەستکرد.'
            ], 403);
        }

        $branchId = $request->query('branch_id');
        $data = SmartAnalyticsService::getLiquidityAndAnomalies($branchId);

        return response()->json($data);
    }
}
