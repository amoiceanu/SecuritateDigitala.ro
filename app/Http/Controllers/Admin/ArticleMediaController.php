<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleMediaPlacement;
use App\Services\ArticleMediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ArticleMediaController extends Controller
{
    public function index(Article $article): View
    {
        $article->load(['mediaPlacements.asset']);

        return view('admin.articles.media', compact('article'));
    }

    public function store(Request $request, Article $article, ArticleMediaStorage $storage): RedirectResponse
    {
        $data = $request->validate($this->rules($request, true));
        $this->ensurePositionAvailable($article, $data['position'], (int) $data['display_order']);

        DB::transaction(function () use ($request, $article, $data, $storage): void {
            $asset = $storage->store($data['image'], $this->assetData($data));
            $asset->update($this->assetData($data));
            $article->mediaPlacements()->create([
                'media_asset_id' => $asset->id,
                'position' => $data['position'],
                'display_order' => $data['display_order'],
                'anchor_ro' => $data['anchor_ro'] ?? null,
                'anchor_en' => $data['anchor_en'] ?? null,
                'alt_text' => $data['alt_text'],
                'alt_text_en' => $data['alt_text_en'] ?? null,
                'caption' => $data['caption'] ?? null,
                'caption_en' => $data['caption_en'] ?? null,
            ]);
        });

        return back()->with('success', 'Imaginea articolului a fost adăugată.');
    }

    public function update(Request $request, Article $article, ArticleMediaPlacement $placement, ArticleMediaStorage $storage): RedirectResponse
    {
        abort_unless($placement->article_id === $article->id, 404);
        $data = $request->validate($this->rules($request, false));
        $this->ensurePositionAvailable($article, $data['position'], (int) $data['display_order'], $placement->id);

        DB::transaction(function () use ($article, $placement, $data, $storage): void {
            $oldAsset = $placement->asset;
            $asset = $oldAsset;

            if (filled($data['image'] ?? null)) {
                $asset = $storage->store($data['image'], $this->assetData($data));
                $asset->update($this->assetData($data));
            } else {
                $asset->update($this->assetData($data));
            }

            $placement->update([
                'media_asset_id' => $asset->id,
                'position' => $data['position'],
                'display_order' => $data['display_order'],
                'anchor_ro' => $data['anchor_ro'] ?? null,
                'anchor_en' => $data['anchor_en'] ?? null,
                'alt_text' => $data['alt_text'],
                'alt_text_en' => $data['alt_text_en'] ?? null,
                'caption' => $data['caption'] ?? null,
                'caption_en' => $data['caption_en'] ?? null,
            ]);

            if ($oldAsset->is($asset) === false && $oldAsset->placements()->doesntExist()) {
                $oldAsset->deleteStoredFiles();
                $oldAsset->delete();
            }
        });

        return back()->with('success', 'Imaginea articolului a fost actualizată.');
    }

    public function destroy(Article $article, ArticleMediaPlacement $placement): RedirectResponse
    {
        abort_unless($placement->article_id === $article->id, 404);
        $asset = $placement->asset;
        $placement->delete();

        if ($asset->placements()->doesntExist()) {
            $asset->deleteStoredFiles();
            $asset->delete();
        }

        return back()->with('success', 'Imaginea a fost eliminată din articol.');
    }

    private function rules(Request $request, bool $requiredImage): array
    {
        return [
            'image' => [$requiredImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:12288'],
            'image_type' => ['required', Rule::in(['photo', 'screenshot', 'mockup', 'ai'])],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'photographer' => ['nullable', 'string', 'max:255'],
            'license_name' => ['nullable', 'string', 'max:120'],
            'license_url' => ['nullable', 'url', 'max:2048'],
            'position' => ['required', Rule::in(['hero', 'inline'])],
            'display_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'anchor_ro' => [Rule::requiredIf($request->input('position') === 'inline'), 'nullable', 'string', 'max:180'],
            'anchor_en' => ['nullable', 'string', 'max:180'],
            'alt_text' => ['required', 'string', 'max:500'],
            'alt_text_en' => ['nullable', 'string', 'max:500'],
            'caption' => ['nullable', 'string', 'max:500'],
            'caption_en' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function assetData(array $data): array
    {
        return [
            'image_type' => $data['image_type'],
            'source_url' => $data['source_url'] ?? null,
            'photographer' => $data['photographer'] ?? null,
            'license_name' => $data['license_name'] ?? null,
            'license_url' => $data['license_url'] ?? null,
        ];
    }

    private function ensurePositionAvailable(Article $article, string $position, int $order, ?int $ignoreId = null): void
    {
        $exists = $article->mediaPlacements()
            ->where('position', $position)
            ->when($position === 'inline', fn ($query) => $query->where('display_order', $order))
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'display_order' => $position === 'hero'
                    ? 'Articolul are deja o imagine principală.'
                    : 'Există deja o imagine cu această poziție și ordine în articol.',
            ]);
        }
    }
}
