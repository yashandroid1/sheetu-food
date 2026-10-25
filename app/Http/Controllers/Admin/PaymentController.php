<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paymentsys;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $mobileSearch = $request->query('mobile_search', '');
        $fromDate = $request->query('from_date', '');
        $toDate = $request->query('to_date', '');
        $packageFilter = $request->query('pkg', 'all');

        $packages = Paymentsys::select('pkg')->distinct()->whereNotNull('pkg')->orderBy('pkg', 'asc')->pluck('pkg');

        $query = Paymentsys::from('paymentsys as p')
            ->leftJoin('users as u', 'p.mobile', '=', 'u.mobile')
            ->select('p.*', 'u.name', 'u.email');

        if (!empty($mobileSearch)) {
            $query->where('p.mobile', 'like', "%{$mobileSearch}%");
        }

        if (!empty($fromDate)) {
            $query->where('p.date_time', '>=', "{$fromDate} 00:00:00");
        }

        if (!empty($toDate)) {
            $query->where('p.date_time', '<=', "{$toDate} 23:59:59");
        }

        if (!empty($packageFilter) && $packageFilter !== 'all') {
            $query->where('p.pkg', $packageFilter);
        }

        $rawPayments = $query->orderBy('p.date_time', 'desc')->get();

        $paymentsGrouped = [];
        $totalPrice = 0;
        $totalPaid = 0;

        foreach ($rawPayments as $p) {
            $mobile = $p->mobile;
            $dt = Carbon::parse($p->date_time);
            $month = $dt->format('Y-m');
            $key = $mobile . '_' . $month;

            if (!isset($paymentsGrouped[$key])) {
                $paymentsGrouped[$key] = [
                    'mobile'        => $mobile,
                    'name'          => $p->name ?: 'N/A',
                    'email'         => $p->email ?: 'N/A',
                    'month'         => $month,
                    'month_display' => $dt->format('M Y'),
                    'pkg'           => $p->pkg,
                    'price'         => (float) $p->price,
                    'paid'          => 0,
                    'remain'        => 0,
                    'latest_date'   => $p->date_time,
                    'payment_dates' => [],
                ];
            }

            $paymentsGrouped[$key]['paid'] += (float) $p->paid;
            $paymentsGrouped[$key]['payment_dates'][] = [
                'date'  => $p->date_time,
                'price' => (float) $p->price,
                'paid'  => (float) $p->paid,
            ];

            if ($dt->greaterThan(Carbon::parse($paymentsGrouped[$key]['latest_date']))) {
                $paymentsGrouped[$key]['latest_date'] = $p->date_time;
                $paymentsGrouped[$key]['pkg'] = $p->pkg;
            }
        }

        foreach ($paymentsGrouped as $key => $group) {
            $remain = $group['price'] - $group['paid'];
            $paymentsGrouped[$key]['remain'] = $remain;
            $totalPrice += $group['price'];
            $totalPaid += $group['paid'];
        }

        $totalPending = $totalPrice - $totalPaid;

        // Sort by latest date descending
        usort($paymentsGrouped, function ($a, $b) {
            return strtotime($b['latest_date']) - strtotime($a['latest_date']);
        });

        $hasFilters = !empty($mobileSearch) || !empty($fromDate) || !empty($toDate) || (!empty($packageFilter) && $packageFilter !== 'all');

        return view('admin.payments.index', compact(
            'paymentsGrouped',
            'packages',
            'totalPrice',
            'totalPaid',
            'totalPending',
            'mobileSearch',
            'fromDate',
            'toDate',
            'packageFilter',
            'hasFilters'
        ));
    }
}
