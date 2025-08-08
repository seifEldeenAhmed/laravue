<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            if (!auth()->user() instanceof \App\Models\Admin) {
                $this->responseWithUnauthorized();
            }
            return $this->responseWithSuccess([
                'stats' => new StatsResource(auth()->user())
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to access admin dashboard', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to access admin dashboard: ' . $e->getMessage(), 500);
        }
    }
}
