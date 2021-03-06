<?php

namespace App\Http\Controllers;

use App\Models\Search;
use App\Models\Term;
use App\Models\TwitterAsk;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SummaryController extends Controller
{
    /**
     * @param Request $request
     * @return View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function weekly(Request $request): View
    {
        $this->validate($request, [
            'sub' => ['nullable', 'integer'],
        ]);

        $expires = app()->environment('locale') ? CarbonImmutable::now()->addSecond() : CarbonImmutable::now()->addHour();

        $start = Carbon::now()->locale('id_ID')->subDay(now()->format('w'));
        $end = $start->copy()->addWeek();

        if (!empty($request->sub)) {
            $start->subWeek($request->sub);
            $end->subWeek($request->sub);
        }

        $count['new_word'] = Cache::remember('word.count_new', $expires, function () use ($start, $end) {
            return Term::whereDate('created_at', '>=', $start->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end->format('Y-m-d'))
                ->count();
        });

        $count['total_word'] = Cache::remember('word.count', $expires, function () use ($start, $end) {
            return Term::count();
        });

        $count['new_user'] = Cache::remember('user.count_new', $expires, function () use ($start, $end) {
            return User::whereDate('created_at', '>=', $start->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end->format('Y-m-d'))
                ->count();
        });

        $count['total_user'] = Cache::remember('user.count', $expires, function () use ($start, $end) {
            return User::count();
        });

        $count['new_search'] = Cache::remember('search.count_new', $expires, function () use ($start, $end) {
            return Search::where('created_at', '>=', $start->format('Y-m-d'))
                ->where('created_at', '<=', $end->format('Y-m-d'))
                ->count();
        });

        $count['total_search'] = Cache::remember('search.count', $expires, function () {
            return Search::count();
        });

        // search via twitter
        $count['new_twitter_question'] = TwitterAsk::whereDate('created_at', '>=', $start->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end->format('Y-m-d'))
            ->whereIsReplied(true)
            ->count();
        $count['total_twitter_question'] = TwitterAsk::whereIsReplied(true)->count();

        $terms = Term::orderByDesc('created_at')
            ->where('created_at', '>=', $start->format('Y-m-d'))
            ->where('created_at', '<=', $end->format('Y-m-d'))
            ->get();

        $number = new \NumberFormatter('id_ID', \NumberFormatter::DECIMAL);

        return view('summary.weekly', compact('count', 'number', 'start', 'end'))
            ->with('terms', $terms)
            ->with('title', __('Ringkasan Mingguan (:day_start :week_start - :day_end :week_end)', [
                'day_start' => $start->format('d'),
                'week_start' => $start->monthName,
                'day_end' => $end->format('d'),
                'week_end' => $end->monthName,
            ]))
            ->with('description', __('Lihat ringkasan kegiatan di :app dalam seminggu', ['app' => config('app.name')]));
    }
}
