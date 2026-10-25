<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FdDistribute;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DistributionController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $startDate = $request->query('start_date', $today);
        $endDate = $request->query('end_date', $today);
        $mobile = $request->query('mobile', '');
        $mealType = $request->query('meal_type', '');

        $isTodayView = ($startDate === $today && $endDate === $today && empty($mobile) && empty($mealType));

        $query = FdDistribute::query();

        if (!empty($startDate)) {
            $query->where('date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('date', '<=', $endDate);
        }

        if (!empty($mobile)) {
            $query->where('mobile', 'like', "%{$mobile}%");
        }

        if (!empty($mealType) && in_array($mealType, ['brkf', 'luh', 'dinr'])) {
            $query->where($mealType, 1);
        }

        // Clone query for stats
        $statsQuery = clone $query;
        $stats = [
            'total_distributions' => $statsQuery->count(),
            'total_amount'        => (float) $statsQuery->sum('amount'),
            'total_breakfast'     => (int) $statsQuery->sum('brkf'),
            'total_lunch'         => (int) $statsQuery->sum('luh'),
            'total_dinner'        => (int) $statsQuery->sum('dinr'),
        ];

        $records = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->get();
        $numRecords = $records->count();

        $emptyMessage = $isTodayView ? "No distributions today" : "No distribution records found";

        return view('admin.distributes.index', compact(
            'records',
            'numRecords',
            'stats',
            'startDate',
            'endDate',
            'mobile',
            'mealType',
            'isTodayView',
            'emptyMessage'
        ));
    }
}
