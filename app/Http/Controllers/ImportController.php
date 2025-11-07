<?php

namespace App\Http\Controllers;

use App\Enums\ImportSource;
use App\Repositories\PostRepositoryInterface;
use App\Services\ImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(
        private ImportService $importService,
        private PostRepositoryInterface $postRepository,
    ) {
    }

    /**
     * Display the import page
     */
    public function index()
    {
        $recentImports = $this->postRepository->getRecentImports(10);

        return view('import.index', compact('recentImports'));
    }

    /**
     * Import a post from the specified source
     */
    public function import(Request $request, string $source)
    {
        try {
            $importSource = ImportSource::from($source);
        } catch (\ValueError $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid source. Allowed sources: ' . implode(', ', ImportSource::values()),
                ], 400);
            }

            return redirect()->route('import.index')
                ->with('error', 'Invalid source');
        }

        $result = $this->importService->importFrom($importSource);

        if ($request->expectsJson()) {
            return response()->json($result->toArray());
        }

        if ($result->success) {
            return redirect()->route('import.index')
                ->with('success', $result->message);
        }

        return redirect()->route('import.index')
            ->with($result->duplicate ? 'warning' : 'error', $result->message);
    }
}
