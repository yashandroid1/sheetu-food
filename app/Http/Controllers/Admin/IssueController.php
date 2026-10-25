<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issues;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', '');
        $typeFilter = $request->query('type', '');
        $mobileFilter = $request->query('mobile', '');

        $query = Issues::from('issue_requests as ir')
            ->leftJoin('users as u', 'ir.token', '=', 'u.token')
            ->select('ir.*', 'u.name as user_name');

        if (!empty($statusFilter) && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
            $query->where('ir.status', $statusFilter);
        }

        if (!empty($typeFilter)) {
            $query->where('ir.type', 'like', "%{$typeFilter}%");
        }

        if (!empty($mobileFilter)) {
            $query->where('ir.mobile', 'like', "%{$mobileFilter}%");
        }

        // Stats query with same filters
        $statsQuery = clone $query;
        $stats = [
            'total_issues'   => $statsQuery->count(),
            'pending_count'  => (clone $query)->where('ir.status', 'pending')->count(),
            'approved_count' => (clone $query)->where('ir.status', 'approved')->count(),
            'rejected_count' => (clone $query)->where('ir.status', 'rejected')->count(),
        ];

        $records = $query->orderBy('ir.created_at', 'desc')->get();

        return view('admin.issues.index', compact(
            'records',
            'stats',
            'statusFilter',
            'typeFilter',
            'mobileFilter'
        ));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'issue_id' => 'required|integer',
            'status'   => 'required|in:pending,approved,rejected',
        ]);

        $issue = Issues::findOrFail($request->input('issue_id'));
        $issue->status = $request->input('status');
        $issue->save();

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'msg'  => 'Status updated successfully!'
        ]);
    }
}
