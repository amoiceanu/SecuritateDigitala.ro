<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\EditorialCalendarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialCalendarTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function import(): void
    {
        $this->seed(EditorialCalendarSeeder::class);
    }

    private function data(Article $a, array $changes = []): array
    {
        return array_replace(['title' => $a->title, 'slug' => $a->slug, 'category_id' => $a->category_id, 'author' => $a->author, 'reading_time' => 1, 'status' => 'draft', 'editorial_date' => $a->editorial_date?->format('Y-m-d'), 'access_type' => $a->access_type, 'excerpt' => '', 'content' => '', 'calendar' => 1], $changes);
    }

    public function test_import_creates_exact_plan_as_empty_unpublished_drafts(): void
    {
        $this->artisan('editorial:seed')->assertSuccessful();
        $rows = json_decode(file_get_contents(database_path('data/editorial-plan-2026-2027.json')), true);
        $this->assertCount(42, $rows);
        $this->assertDatabaseCount('articles', 42);
        foreach ($rows as $row) {
            $a = Article::with('category')->where('editorial_key', $row['key'])->firstOrFail();
            $this->assertSame($row['title'], $a->title);
            $this->assertSame($row['editorial_date'], $a->editorial_date->format('Y-m-d'));
            $this->assertSame($row['category_slug'], $a->category->slug);
            $this->assertSame($row['access_type'], $a->access_type);
            $this->assertSame('draft', $a->status);
            $this->assertSame('', $a->content);
            $this->assertSame('', $a->excerpt);
            $this->assertNull($a->published_at);
        }
        $this->assertCount(14, Article::all()->groupBy(fn ($a) => $a->editorial_date->format('Y-m')));
        foreach (Article::all()->groupBy(fn ($a) => $a->editorial_date->format('Y-m')) as $month) {
            $this->assertCount(3, $month);
            $this->assertSame(['05', '15', '25'], $month->map(fn ($a) => $a->editorial_date->format('d'))->all());
        }
        $this->assertSame(28, Article::where('access_type', 'public')->count());
        $this->assertSame(14, Article::where('access_type', 'premium')->count());
    }

    public function test_import_is_repeatable_preserves_edits_and_does_not_touch_existing_articles(): void
    {
        $c = Category::create(['name' => 'Fraude online', 'slug' => 'fraude-online']);
        $existing = Article::create(['category_id' => $c->id, 'title' => 'Original', 'slug' => 'black-friday-cum-verifici-un-magazin-online-inainte-sa-platesti', 'excerpt' => 'Existing', 'content' => 'Existing body', 'author' => 'Editor', 'status' => 'published', 'published_at' => '2026-01-01']);
        $before = $existing->fresh()->getRawOriginal();
        $this->import();
        $draft = Article::where('editorial_key', 'plan-2026-11-1')->firstOrFail();
        $this->assertNotSame($existing->slug, $draft->slug);
        $draft->update(['title' => 'Modificat de editor', 'slug' => 'modificat', 'editorial_date' => '2027-03-09', 'content' => 'Text scris manual']);
        $this->import();
        $this->assertDatabaseCount('articles', 43);
        $this->assertSame('Modificat de editor', $draft->fresh()->title);
        $this->assertSame('Text scris manual', $draft->fresh()->content);
        $this->assertSame('2027-03-09', $draft->fresh()->editorial_date->format('Y-m-d'));
        $this->assertSame($before, $existing->fresh()->getRawOriginal());
    }

    public function test_calendar_uses_existing_admin_authorization(): void
    {
        $this->get(route('admin.editorial.index'))->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->get(route('admin.editorial.index'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('admin.editorial.index'))->assertOk()->assertSee('Calendar editorial');
    }

    public function test_month_filters_and_navigation(): void
    {
        $this->import();
        $this->actingAs($this->admin());
        $a = Article::where('editorial_key', 'plan-2026-11-1')->firstOrFail();
        $premium = Article::where('editorial_key', 'plan-2026-11-3')->firstOrFail();
        $this->get(route('admin.editorial.index', ['month' => '2026-11']))->assertOk()->assertViewHas('articles', fn ($rows) => $rows->count() === 3)->assertSee($a->title)->assertSee('month=2026-12', false);
        $this->get(route('admin.editorial.index', ['month' => '2026-11', 'access_type' => 'premium']))->assertOk()->assertViewHas('articles', fn ($rows) => $rows->count() === 1 && $rows->first()->id === $premium->id);
        $this->get(route('admin.editorial.index', ['month' => '2026-11', 'category_id' => $a->category_id]))->assertViewHas('articles', fn ($rows) => $rows->count() === 2);
        $this->get(route('admin.editorial.index', ['month' => '2026-11', 'status' => 'published']))->assertViewHas('articles', fn ($rows) => $rows->isEmpty());
        $this->get(route('admin.editorial.index', ['month' => '2026-02']))->assertViewHas('days', fn ($days) => count($days) === 35);
        $this->get(route('admin.editorial.index', ['month' => '2026-13']))->assertSessionHasErrors('month');
    }

    public function test_existing_form_can_create_edit_reschedule_and_delete_empty_draft(): void
    {
        $this->import();
        $this->actingAs($this->admin());
        $a = Article::first();
        $this->get(route('admin.articole.create', ['calendar' => 1, 'editorial_date' => '2026-11-05']))->assertOk()->assertSee('Data propusă');
        $this->post(route('admin.articole.store'), $this->data($a, ['title' => 'Draft nou', 'slug' => '', 'editorial_date' => '2026-12-12']))->assertRedirect(route('admin.editorial.index', ['month' => '2026-12']));
        $draft = Article::where('slug', 'draft-nou')->firstOrFail();
        $this->assertSame('', $draft->content);
        $this->assertNull($draft->published_at);
        $this->put(route('admin.articole.update', $draft), $this->data($draft, ['title' => 'Titlu schimbat', 'editorial_date' => '2027-03-28', 'access_type' => 'premium']))->assertRedirect(route('admin.editorial.index', ['month' => '2027-03']));
        $this->assertSame('draft', $draft->fresh()->status);
        $this->assertSame('premium', $draft->fresh()->access_type);
        $this->assertSame('2027-03-28', $draft->fresh()->editorial_date->format('Y-m-d'));
        $this->get(route('admin.editorial.index', ['month' => '2026-12']))->assertViewHas('articles', fn ($rows) => ! $rows->contains('id', $draft->id));
        $this->delete(route('admin.articole.destroy', $draft))->assertRedirect();
        $this->assertModelMissing($draft);
    }

    public function test_editorial_date_passing_does_not_publish_or_expose_drafts(): void
    {
        $this->import();
        $a = Article::where('editorial_key', 'plan-2026-11-1')->firstOrFail();
        $this->travelTo(now()->setDate(2028, 1, 1));
        $this->get('/')->assertOk()->assertDontSee($a->title);
        $this->get('/articole?q='.urlencode($a->title))->assertOk()->assertViewHas('articles', fn ($rows) => $rows->isEmpty());
        $this->get(route('articles.show', $a))->assertNotFound();
        $this->get(route('sections.show', $a->category))->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/articole/'.$a->slug);
        $this->assertSame(42, Article::where('status', 'draft')->whereNull('published_at')->count());
    }

    public function test_manual_publication_requires_content_and_never_uses_editorial_date(): void
    {
        $this->import();
        $this->actingAs($this->admin());
        $a = Article::first();
        $this->put(route('admin.articole.update', $a), $this->data($a, ['status' => 'published']))->assertSessionHasErrors(['content', 'excerpt']);
        $this->assertSame('draft', $a->fresh()->status);
        $this->put(route('admin.articole.update', $a), $this->data($a, ['status' => 'published', 'access_type' => 'premium', 'content' => 'Text completat manual', 'excerpt' => 'Rezumat manual']))->assertRedirect();
        $this->get(route('articles.show', $a))->assertOk()->assertSee('Text completat manual');
        $this->assertSame('published', $a->fresh()->status);
        $this->assertSame(now()->toDateString(), $a->fresh()->published_at->format('Y-m-d'));
    }

    public function test_invalid_basic_fields_do_not_change_the_draft(): void
    {
        $this->import();
        $this->actingAs($this->admin());
        $a = Article::first();
        $this->put(route('admin.articole.update',$a),$this->data($a,['editorial_date' => '2027-02-30', 'access_type' => 'secret']))->assertSessionHasErrors(['editorial_date', 'access_type']);
        $this->assertSame('2026-11-05',$a->fresh()->editorial_date->format('Y-m-d'));
    }
}
