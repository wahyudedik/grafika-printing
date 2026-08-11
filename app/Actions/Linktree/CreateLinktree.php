<?php

namespace App\Actions\Linktree;

use App\Actions\BaseAction;
use App\Models\Vendor\Linktree;

class CreateLinktree extends BaseAction
{
    /**
     * Create a new Linktree with default values and colors.
     *
     * Expected data:
     * - vendor_id (int)
     * - name (string)
     * - template (string)
     * - custom_url (string)
     * - primary_color (string, optional)
     * - secondary_color (string, optional)
     * - bg_color (string, optional)
     * - text_color (string, optional)
     * + other Linktree fields
     */
    public function handle(array $data): Linktree
    {
        // Set defaults
        $data['is_active'] = false;
        $data['views_count'] = 0;
        $data['clicks_count'] = 0;

        // Set default colors based on template
        $data['primary_color'] = $data['primary_color'] ?? $this->getDefaultColor($data['template'], 'primary');
        $data['secondary_color'] = $data['secondary_color'] ?? $this->getDefaultColor($data['template'], 'secondary');
        $data['bg_color'] = $data['bg_color'] ?? $this->getDefaultColor($data['template'], 'bg');
        $data['text_color'] = $data['text_color'] ?? $this->getDefaultColor($data['template'], 'text');

        return Linktree::create($data);
    }

    /**
     * Get default color for a given template and type.
     */
    protected function getDefaultColor(string $template, string $type): string
    {
        $colors = [
            'minimal' => [
                'primary' => '#000000',
                'secondary' => '#374151',
                'bg' => '#ffffff',
                'text' => '#111827',
            ],
            'colorful' => [
                'primary' => '#6366f1',
                'secondary' => '#8b5cf6',
                'bg' => '#faf5ff',
                'text' => '#1e1b4b',
            ],
            'dark' => [
                'primary' => '#8b5cf6',
                'secondary' => '#a78bfa',
                'bg' => '#111827',
                'text' => '#f9fafb',
            ],
            'professional' => [
                'primary' => '#1e40af',
                'secondary' => '#3b82f6',
                'bg' => '#eff6ff',
                'text' => '#1e293b',
            ],
            'gradient' => [
                'primary' => '#ec4899',
                'secondary' => '#f472b6',
                'bg' => '#fdf2f8',
                'text' => '#831843',
            ],
            'nature' => [
                'primary' => '#059669',
                'secondary' => '#10b981',
                'bg' => '#ecfdf5',
                'text' => '#064e3b',
            ],
            'neon' => [
                'primary' => '#06b6d4',
                'secondary' => '#22d3ee',
                'bg' => '#0f172a',
                'text' => '#e2e8f0',
            ],
            'elegant' => [
                'primary' => '#92400e',
                'secondary' => '#b45309',
                'bg' => '#fffbeb',
                'text' => '#451a03',
            ],
        ];

        return $colors[$template][$type] ?? '#000000';
    }
}
