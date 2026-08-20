<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\WebsiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Kelola Website (khusus Super Admin):
 * seluruh teks & gambar landing page dikelola dari sini.
 */
class WebsiteController extends Controller
{
    public function index(): Response
    {
        return response()->view('admin.website', [
            'content' => WebsiteContent::all(),
            'heroImage' => WebsiteContent::heroImageUrl(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.seo' => ['required', 'array'],
            'sections.hero' => ['required', 'array'],
            'sections.features' => ['required', 'array'],
            'sections.workflow' => ['required', 'array'],
            'sections.stats' => ['required', 'array'],
            'sections.modules' => ['required', 'array'],
            'sections.cta' => ['required', 'array'],
            'sections.footer' => ['required', 'array'],

            'sections.seo.title' => ['required', 'string', 'max:190'],
            'sections.seo.description' => ['required', 'string', 'max:500'],

            'sections.hero.badge' => ['required', 'string', 'max:150'],
            'sections.hero.title_start' => ['required', 'string', 'max:100'],
            'sections.hero.title_end' => ['required', 'string', 'max:150'],
            'sections.hero.typing_words' => ['required', 'string', 'max:300'],
            'sections.hero.lead' => ['required', 'string', 'max:1000'],
            'sections.hero.cta_primary' => ['required', 'string', 'max:60'],
            'sections.hero.cta_secondary' => ['required', 'string', 'max:60'],
            'sections.hero.scroll_hint' => ['required', 'string', 'max:80'],
            'sections.hero.highlights' => ['nullable', 'array', 'max:6'],
            'sections.hero.highlights.*' => ['nullable', 'string', 'max:100'],

            'sections.features.eyebrow' => ['required', 'string', 'max:80'],
            'sections.features.title' => ['required', 'string', 'max:150'],
            'sections.features.subtitle' => ['required', 'string', 'max:300'],
            'sections.features.items' => ['required', 'array', 'min:1', 'max:9'],
            'sections.features.items.*.icon' => ['required', 'string', 'max:60'],
            'sections.features.items.*.color' => ['required', 'in:blue,green,amber,purple'],
            'sections.features.items.*.title' => ['required', 'string', 'max:100'],
            'sections.features.items.*.points' => ['required', 'string', 'max:800'],

            'sections.workflow.eyebrow' => ['required', 'string', 'max:80'],
            'sections.workflow.title' => ['required', 'string', 'max:150'],
            'sections.workflow.steps' => ['required', 'array', 'min:1', 'max:8'],
            'sections.workflow.steps.*.icon' => ['required', 'string', 'max:60'],
            'sections.workflow.steps.*.accent' => ['required', 'in:blue,green,amber'],
            'sections.workflow.steps.*.title' => ['required', 'string', 'max:100'],
            'sections.workflow.steps.*.desc' => ['required', 'string', 'max:200'],

            'sections.stats.items' => ['required', 'array', 'min:1', 'max:6'],
            'sections.stats.items.*.value' => ['required', 'string', 'max:20'],
            'sections.stats.items.*.suffix' => ['nullable', 'string', 'max:10'],
            'sections.stats.items.*.label' => ['required', 'string', 'max:100'],

            'sections.modules.eyebrow' => ['required', 'string', 'max:80'],
            'sections.modules.title' => ['required', 'string', 'max:150'],
            'sections.modules.items' => ['required', 'array', 'min:1', 'max:6'],
            'sections.modules.items.*.icon' => ['required', 'string', 'max:60'],
            'sections.modules.items.*.color' => ['required', 'in:blue,green,amber'],
            'sections.modules.items.*.title' => ['required', 'string', 'max:100'],
            'sections.modules.items.*.desc' => ['required', 'string', 'max:700'],
            'sections.modules.items.*.tags' => ['nullable', 'string', 'max:300'],

            'sections.cta.title' => ['required', 'string', 'max:150'],
            'sections.cta.text' => ['required', 'string', 'max:400'],
            'sections.cta.button' => ['required', 'string', 'max:60'],

            'sections.footer.tagline' => ['required', 'string', 'max:150'],
            'sections.footer.copyright' => ['required', 'string', 'max:150'],

            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ], [
            'sections.*' => 'Data konten tidak valid.',
            'hero_image.image' => 'File harus berupa gambar.',
            'hero_image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'hero_image.max' => 'Ukuran gambar maksimal 3 MB.',
        ]);

        $sections = $validated['sections'];

        // Bersihkan baris kosong pada daftar item.
        $sections['hero']['highlights'] = array_values(array_filter(
            array_map('trim', $sections['hero']['highlights'] ?? []),
            fn ($line) => $line !== ''
        ));

        foreach (['features', 'workflow', 'stats', 'modules'] as $listSection) {
            $key = $listSection === 'workflow' ? 'steps' : 'items';
            // Stats memakai "label" sebagai penanda isi, section lain "title".
            $titleKey = $listSection === 'stats' ? 'label' : 'title';

            $sections[$listSection][$key] = array_values(array_filter(
                $sections[$listSection][$key],
                fn ($item) => trim($item[$titleKey] ?? '') !== ''
            ));
        }

        Setting::forgetCache();

        foreach (WebsiteContent::SECTIONS as $section) {
            WebsiteContent::set($section, $sections[$section]);
        }

        $this->handleHeroImage($request);

        Setting::forgetCache();

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Konten website berhasil diperbarui.');
    }

    /**
     * Kembalikan satu section ke nilai bawaan.
     */
    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section' => ['required', 'in:'.implode(',', WebsiteContent::SECTIONS)],
        ]);

        WebsiteContent::reset($validated['section']);
        Setting::forgetCache();

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Section "'.$validated['section'].'" dikembalikan ke bawaan.');
    }

    private function handleHeroImage(Request $request): void
    {
        if ($request->boolean('remove_hero_image')) {
            $this->deleteHeroImageFile();

            Setting::set('web_hero_image', null);

            return;
        }

        if (! $request->hasFile('hero_image')) {
            return;
        }

        $this->deleteHeroImageFile();

        $file = $request->file('hero_image');
        $name = 'hero-'.now()->format('Ymd-His').'.'.$file->getClientOriginalExtension();

        $file->move(public_path('uploads'), $name);

        Setting::set('web_hero_image', 'uploads/'.$name);
    }

    private function deleteHeroImageFile(): void
    {
        $old = Setting::get('web_hero_image');

        if ($old && is_file(public_path($old))) {
            @unlink(public_path($old));
        }
    }
}
