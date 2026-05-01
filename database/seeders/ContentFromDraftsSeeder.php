<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use App\Models\HandbookPart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentFromDraftsSeeder extends Seeder
{
    /**
     * Maps draft filename → [slug, part_code, order, title_override]
     * Part codes correspond to HandbookPartsSeeder parts A-M.
     */
    private array $map = [
        'handbook-about-unijaya.md'         => ['about-unijaya',               'A', 1, 'About Unijaya'],
        'code-of-conduct.md'                => ['code-of-conduct',             'G', 1, 'Code of Conduct'],
        'anti-sexual-harassment-policy.md'  => ['anti-sexual-harassment',      'H', 1, 'Anti-Sexual Harassment Policy'],
        'grievance-policy.md'               => ['grievance-policy',            'H', 2, 'Grievance Policy'],
        'data-protection-policy.md'         => ['data-protection-policy',      'H', 5, 'Data Protection Policy (PDPA)'],
        'wfh-remote-work-policy.md'         => ['wfh-remote-work-policy',      'D', 4, 'Work From Home / Remote Work Policy'],
        'health-safety-policy.md'           => ['health-safety-policy',        'J', 1, 'Health & Safety Policy'],
    ];

    public function run(): void
    {
        $draftsPath = base_path('../../../../drafts');

        // Resolve the actual path relative to webapp-new
        $candidates = [
            base_path('../drafts'),
            base_path('../../drafts'),
            base_path('../../../drafts'),
            'C:/Users/User/Desktop/Claude/ClaudeCode/Aril/HRAI/drafts',
        ];

        $draftsDir = null;
        foreach ($candidates as $path) {
            if (is_dir($path)) {
                $draftsDir = $path;
                break;
            }
        }

        if (! $draftsDir) {
            $this->command?->warn('Drafts directory not found. Skipping ContentFromDraftsSeeder.');
            return;
        }

        $parts = HandbookPart::pluck('id', 'code');
        $imported = 0;

        foreach ($this->map as $filename => [$slug, $partCode, $order, $titleOverride]) {
            $filePath = rtrim($draftsDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

            if (! file_exists($filePath)) {
                $this->command?->warn("Draft not found: {$filename}");
                continue;
            }

            $raw = file_get_contents($filePath);

            // Strip leading H1 (use as title if no override)
            $title = $titleOverride;
            $raw   = preg_replace('/^#\s+.+\n?/', '', $raw, 1);

            // Convert markdown → HTML
            $html = Str::markdown($raw, ['html_input' => 'allow']);

            ContentSection::updateOrCreate(
                ['category' => 'handbook', 'slug' => $slug],
                [
                    'title'            => $title,
                    'body'             => $html,
                    'order'            => $order,
                    'is_published'     => true,
                    'part_id'          => $parts[$partCode] ?? null,
                    'last_updated_by'  => null,
                ]
            );

            $imported++;
            $this->command?->info("Imported: {$filename} → Part {$partCode}");
        }

        $this->command?->info("Done: {$imported} sections imported from drafts.");
    }
}
