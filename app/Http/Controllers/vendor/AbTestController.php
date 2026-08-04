<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\LinktreeAbTest;
use App\Models\LinktreeAbTestResult;
use App\Models\Vendor\Linktree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbTestController extends Controller
{
    /**
     * Display list of A/B tests for a linktree.
     */
    public function index(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $abTests = $linktree->abTests()
            ->withCount(['results', 'results as clicks_count' => function ($query) {
                $query->where('is_click', true);
            }])
            ->orderByDesc('created_at')
            ->get();

        return view('vendor.linktree.ab-test.index', compact('linktree', 'abTests'));
    }

    /**
     * Show form to create a new A/B test.
     */
    public function create(Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        // Can't create if there's already a running test
        $runningTest = $linktree->abTests()->where('status', 'running')->first();
        if ($runningTest) {
            return redirect()->route('vendor.linktree.ab-test.index', $linktree)
                ->with('error', 'Ada A/B test yang sedang berjalan. Hentikan terlebih dahulu.');
        }

        $templates = ['minimal', 'colorful', 'dark', 'professional'];

        return view('vendor.linktree.ab-test.create', compact('linktree', 'templates'));
    }

    /**
     * Store a new A/B test.
     */
    public function store(Request $request, Linktree $linktree)
    {
        $this->authorizeLinktree($linktree);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'variant_a' => 'required|in:minimal,colorful,dark,professional',
            'variant_b' => 'required|in:minimal,colorful,dark,professional',
            'variant_a' => [
                'required',
                'in:minimal,colorful,dark,professional',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === $request->input('variant_b')) {
                        $fail('Variant A dan Variant B harus berbeda.');
                    }
                },
            ],
            'traffic_split' => 'required|integer|min:10|max:90',
            'min_samples' => 'required|integer|min:50|max:10000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $abTest = $linktree->abTests()->create([
            'name' => $validated['name'],
            'variant_a' => $validated['variant_a'],
            'variant_b' => $validated['variant_b'],
            'traffic_split' => $validated['traffic_split'],
            'min_samples' => $validated['min_samples'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('vendor.linktree.ab-test.index', $linktree)
            ->with('success', 'A/B test berhasil dibuat. Klik "Mulai" untuk memulai test.');
    }

    /**
     * Show A/B test details and results.
     */
    public function show(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        $abTest->load('linktree');

        // Get stats for each variant
        $statsA = [
            'impressions' => $abTest->getImpressions('variant_a'),
            'clicks' => $abTest->getClicks('variant_a'),
            'conversion_rate' => $abTest->getConversionRate('variant_a'),
        ];

        $statsB = [
            'impressions' => $abTest->getImpressions('variant_b'),
            'clicks' => $abTest->getClicks('variant_b'),
            'conversion_rate' => $abTest->getConversionRate('variant_b'),
        ];

        $winner = $abTest->evaluate();

        // Statistical significance (simple Z-test approximation)
        $significance = $this->calculateSignificance($statsA, $statsB);

        return view('vendor.linktree.ab-test.show', compact('linktree', 'abTest', 'statsA', 'statsB', 'winner', 'significance'));
    }

    /**
     * Start an A/B test.
     */
    public function start(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        if ($abTest->status !== 'draft' && $abTest->status !== 'paused') {
            return back()->with('error', 'Test hanya bisa dimulai dari status draft atau dijeda.');
        }

        // Check no other running tests
        $runningTest = $linktree->abTests()
            ->where('status', 'running')
            ->where('id', '!=', $abTest->id)
            ->first();

        if ($runningTest) {
            return back()->with('error', 'Ada A/B test lain yang sedang berjalan. Hentikan terlebih dahulu.');
        }

        $abTest->update([
            'status' => 'running',
            'started_at' => $abTest->started_at ?? now(),
        ]);

        return back()->with('success', 'A/B test berhasil dimulai!');
    }

    /**
     * Pause an A/B test.
     */
    public function pause(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        if ($abTest->status !== 'running') {
            return back()->with('error', 'Test yang dijeda harus dalam status berjalan.');
        }

        $abTest->update(['status' => 'paused']);

        return back()->with('success', 'A/B test berhasil dijeda.');
    }

    /**
     * Stop and complete an A/B test.
     */
    public function stop(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        if (!in_array($abTest->status, ['running', 'paused'])) {
            return back()->with('error', 'Test tidak bisa dihentikan dari status saat ini.');
        }

        $winner = $abTest->evaluate();

        $abTest->update([
            'status' => 'completed',
            'ended_at' => now(),
            'winner' => $winner,
        ]);

        $message = 'A/B test selesai.';
        if ($winner) {
            $winnerTemplate = $winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b;
            $message .= " Pemenang: {$winnerTemplate} ({$winner})";
        } else {
            $message .= ' Tidak ada pemenang yang signifikan.';
        }

        return back()->with('success', $message);
    }

    /**
     * Apply the winning variant as the linktree's template.
     */
    public function applyWinner(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        if ($abTest->status !== 'completed' || !$abTest->winner) {
            return back()->with('error', 'Test belum selesai atau belum ada pemenang.');
        }

        $winnerTemplate = $abTest->winner === 'variant_a' ? $abTest->variant_a : $abTest->variant_b;

        $linktree->update(['template' => $winnerTemplate]);

        return back()->with('success', "Template berhasil diubah ke \"{$winnerTemplate}\" berdasarkan hasil A/B test.");
    }

    /**
     * Delete an A/B test.
     */
    public function destroy(Linktree $linktree, LinktreeAbTest $abTest)
    {
        $this->authorizeLinktree($linktree);
        $this->authorizeAbTest($abTest, $linktree);

        if ($abTest->status === 'running') {
            return back()->with('error', 'Hentikan test terlebih dahulu sebelum menghapus.');
        }

        $abTest->delete();

        return redirect()->route('vendor.linktree.ab-test.index', $linktree)
            ->with('success', 'A/B test berhasil dihapus.');
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    private function authorizeLinktree(Linktree $linktree): void
    {
        $vendor = Auth::user()->vendorUser()->first();
        if ($linktree->vendor_id !== $vendor->id) {
            abort(403, 'Anda tidak memiliki akses ke linktree ini.');
        }
    }

    private function authorizeAbTest(LinktreeAbTest $abTest, Linktree $linktree): void
    {
        if ($abTest->linktree_id !== $linktree->id) {
            abort(403, 'A/B test ini bukan milik linktree ini.');
        }
    }

    /**
     * Simple significance calculation (Z-test approximation for proportions).
     */
    private function calculateSignificance(array $statsA, array $statsB): array
    {
        $nA = $statsA['impressions'];
        $nB = $statsB['impressions'];

        if ($nA < 10 || $nB < 10) {
            return [
                'level' => 'insufficient',
                'label' => 'Data Belum Cukup',
                'color' => 'secondary',
                'message' => 'Perlu minimal 10 impressions per varian.',
            ];
        }

        $pA = $statsA['conversion_rate'] / 100;
        $pB = $statsB['conversion_rate'] / 100;
        $pPool = ($statsA['clicks'] + $statsB['clicks']) / ($nA + $nB);

        if ($pPool === 0 || $pPool === 1) {
            return [
                'level' => 'insufficient',
                'label' => 'Data Belum Cukup',
                'color' => 'secondary',
                'message' => 'Tidak ada konversi yang cukup untuk analisis.',
            ];
        }

        $se = sqrt($pPool * (1 - $pPool) * (1/$nA + 1/$nB));
        $zScore = abs($pA - $pB) / $se;

        // Z-score thresholds: 1.96 = 95%, 2.58 = 99%
        if ($zScore >= 2.58) {
            return [
                'level' => 'high',
                'label' => 'Sangat Signifikan (99%)',
                'color' => 'success',
                'message' => "Z-score: {$zScore}. Perbedaan sangat signifikan secara statistik.",
            ];
        } elseif ($zScore >= 1.96) {
            return [
                'level' => 'medium',
                'label' => 'Signifikan (95%)',
                'color' => 'blue',
                'message' => "Z-score: {$zScore}. Perbedaan signifikan secara statistik.",
            ];
        } else {
            return [
                'level' => 'low',
                'label' => 'Belum Signifikan',
                'color' => 'warning',
                'message' => "Z-score: {$zScore}. Perbedaan belum cukup signifikan. Perlu lebih banyak data.",
            ];
        }
    }
}
