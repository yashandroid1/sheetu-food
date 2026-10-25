<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeekFood;
use Illuminate\Http\Request;

class WeekFoodController extends Controller
{
    public function index()
    {
        $records = WeekFood::orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")->get();

        $totalDays = $records->count();
        $daysWithAllMeals = $records->filter(function ($item) {
            return !empty($item->brkf) && !empty($item->luh) && !empty($item->dinr);
        })->count();

        return view('admin.weekfoods.index', compact('records', 'totalDays', 'daysWithAllMeals'));
    }

    public function handleAction(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'add':
                $request->validate([
                    'day'  => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                    'brkf' => 'required|string|max:255',
                    'luh'  => 'required|string|max:255',
                    'dinr' => 'required|string|max:255',
                ]);

                WeekFood::create([
                    'day'  => $request->input('day'),
                    'brkf' => $request->input('brkf'),
                    'luh'  => $request->input('luh'),
                    'dinr' => $request->input('dinr'),
                ]);

                return redirect()->route('admin.weekfoods.index')->with('flash', [
                    'type' => 'success',
                    'msg'  => 'Week food record added successfully!'
                ]);

            case 'edit':
                $request->validate([
                    'id'   => 'required|integer',
                    'day'  => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                    'brkf' => 'required|string|max:255',
                    'luh'  => 'required|string|max:255',
                    'dinr' => 'required|string|max:255',
                ]);

                $record = WeekFood::findOrFail($request->input('id'));
                $record->update([
                    'day'  => $request->input('day'),
                    'brkf' => $request->input('brkf'),
                    'luh'  => $request->input('luh'),
                    'dinr' => $request->input('dinr'),
                ]);

                return redirect()->route('admin.weekfoods.index')->with('flash', [
                    'type' => 'success',
                    'msg'  => 'Week food record updated successfully!'
                ]);

            case 'delete':
                $request->validate([
                    'id' => 'required|integer',
                ]);

                $record = WeekFood::findOrFail($request->input('id'));
                $record->delete();

                return redirect()->route('admin.weekfoods.index')->with('flash', [
                    'type' => 'success',
                    'msg'  => 'Week food record deleted successfully!'
                ]);

            default:
                return redirect()->route('admin.weekfoods.index');
        }
    }
}
