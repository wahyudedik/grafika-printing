<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Linktree;
use Illuminate\Http\Request;
use App\Http\Concerns\HasVendorContext;
use App\Http\Responses\FlashMessage;



class TemplateController extends Controller
{
    use HasVendorContext;


    /**
     * Available templates with their preview images and descriptions.
     */
    private array $templates = [
        'minimal' => [
            'name' => 'Minimal',
            'description' => 'Tampilan bersih dan simpel dengan fokus pada konten.',
            'preview' => 'minimal-preview.svg',
            'colors' => [
                'primary' => '#374151',
                'secondary' => '#6B7280',
                'bg' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            'button_style' => 'rounded',
        ],
        'colorful' => [
            'name' => 'Colorful',
            'description' => 'Tampilan ceria dengan gradasi warna yang menarik.',
            'preview' => 'colorful-preview.svg',
            'colors' => [
                'primary' => '#8B5CF6',
                'secondary' => '#EC4899',
                'bg' => '#F5F3FF',
                'text' => '#1F2937',
            ],
            'button_style' => 'pill',
        ],
        'dark' => [
            'name' => 'Dark',
            'description' => 'Tampilan gelap yang elegan dan modern.',
            'preview' => 'dark-preview.svg',
            'colors' => [
                'primary' => '#6366F1',
                'secondary' => '#8B5CF6',
                'bg' => '#111827',
                'text' => '#F9FAFB',
            ],
            'button_style' => 'rounded',
        ],
        'professional' => [
            'name' => 'Professional',
            'description' => 'Tampilan formal dan profesional untuk bisnis.',
            'preview' => 'professional-preview.svg',
            'colors' => [
                'primary' => '#1E3A5F',
                'secondary' => '#2563EB',
                'bg' => '#F1F5F9',
                'text' => '#0F172A',
            ],
            'button_style' => 'square',
        ],
    ];

    /**
     * Show template selection page for a linktree.
     */
    public function index(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        return view('vendor.linktree.template', [
            'linktree' => $linktree,
            'templates' => $this->templates,
        ]);
    }

    /**
     * Preview a template with the linktree's current content.
     */
    public function preview(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $request->validate([
            'template' => 'required|in:minimal,colorful,dark,professional',
        ]);

        $template = $request->template;
        $templateConfig = $this->templates[$template];

        // Load linktree relations
        $linktree->load(['activeLinks', 'activeSocials']);

        // Simulate template change without saving
        $previewData = [
            'template' => $template,
            'primary_color' => $request->primary_color ?? $templateConfig['colors']['primary'],
            'secondary_color' => $request->secondary_color ?? $templateConfig['colors']['secondary'],
            'bg_color' => $request->bg_color ?? $templateConfig['colors']['bg'],
            'text_color' => $request->text_color ?? $templateConfig['colors']['text'],
            'button_style' => $request->button_style ?? $templateConfig['button_style'],
        ];

        return response()->json([
            'success' => true,
            'preview' => $previewData,
            'template' => $templateConfig,
        ]);
    }

    /**
     * Apply a template to the linktree.
     */
    public function apply(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'template' => 'required|in:minimal,colorful,dark,professional',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'button_style' => 'nullable|in:rounded,square,pill',
        ]);

        $templateConfig = $this->templates[$validated['template']];

        // Apply template defaults for colors not specified
        $validated['primary_color'] = $validated['primary_color'] ?? $templateConfig['colors']['primary'];
        $validated['secondary_color'] = $validated['secondary_color'] ?? $templateConfig['colors']['secondary'];
        $validated['bg_color'] = $validated['bg_color'] ?? $templateConfig['colors']['bg'];
        $validated['text_color'] = $validated['text_color'] ?? $templateConfig['colors']['text'];
        $validated['button_style'] = $validated['button_style'] ?? $templateConfig['button_style'];

        try {
            $linktree->update($validated);

            return FlashMessage::backSuccess("Template {$templateConfig['name']} berhasil diterapkan!");
        } catch (\Exception $e) {
            \Log::error('Gagal apply template: ' . $e->getMessage());
            return FlashMessage::backError('Gagal menerapkan template. Silakan coba lagi.');
        }
    }

    /**
     * Get template colors via AJAX for live preview.
     */
    public function getColors(string $template)
    {
        if (!isset($this->templates[$template])) {
            return response()->json(['error' => 'Template tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $this->templates[$template],
        ]);
    }

    /**
     * Authorize that the linktree belongs to the current vendor.
     */
    private function authorizeLinktree(Linktree $linktree): void
    {
        $vendor = $this->getVendor();
        if ($linktree->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses ke linktree ini.');
        }
    }
}
