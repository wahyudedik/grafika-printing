<?php

namespace App\Http\Controllers;

use App\Models\Vendor\Linktree;
use Illuminate\Http\Request;

class LinktreePublicController extends Controller
{
    /**
     * Display the public linktree page.
     */
    public function show(string $customUrl)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->with(['activeLinks', 'activeSocials'])
            ->first();

        if (!$linktree) {
            abort(404, 'Linktree tidak ditemukan atau belum aktif.');
        }

        // Increment views count
        $linktree->incrementViews();

        // Get template classes
        $templateClasses = $linktree->getTemplateClasses();

        // Get vendor info
        $vendor = $linktree->vendor;

        return view('linktree.public', compact('linktree', 'templateClasses', 'vendor'));
    }

    /**
     * Track link click and redirect.
     */
    public function trackClick(Request $request, string $customUrl, int $linkId)
    {
        $linktree = Linktree::where('custom_url', $customUrl)
            ->where('is_active', true)
            ->first();

        if (!$linktree) {
            abort(404);
        }

        $link = $linktree->links()->where('id', $linkId)->where('is_active', true)->first();

        if (!$link) {
            abort(404);
        }

        // Increment click counters
        $link->incrementClicks();
        $linktree->incrementClicks();

        return redirect()->away($link->url);
    }
}
