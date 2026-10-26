<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EditorialCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'month' => ['nullable', 'date_format:Y-m', 'regex:/^(19|20|21)[0-9]{2}-(0[1-9]|1[0-2])$/'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'access_type' => ['nullable', Rule::in(['public', 'premium'])],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);
        $month = CarbonImmutable::createFromFormat('!Y-m', $data['month'] ?? now(config('editorial.timezone'))->format('Y-m'), config('editorial.timezone'));
        $query = Article::with('category')->whereBetween('editorial_date', [$month->toDateString(), $month->endOfMonth()->toDateString()]);
        foreach (['category_id', 'access_type', 'status'] as $field) {
            if (filled($data[$field] ?? null)) {
                $query->where($field, $data[$field]);
            }
        }
        $articles = $query->orderBy('editorial_date')->orderBy('title')->get();
        $days = [];
        for ($day = $month->startOfWeek(); $day->lte($month->endOfMonth()->endOfWeek()); $day = $day->addDay()) {
            $days[] = $day;
        }

        return view('admin.editorial.index', [
            'month' => $month, 'days' => $days, 'articles' => $articles, 'byDate' => $articles->groupBy(fn ($article) => $article->editorial_date->format('Y-m-d')),
            'categories' => Category::orderBy('name')->get(), 'filters' => array_filter($data, fn ($value) => $value !== null && $value !== ''),
        ]);
    }
}
