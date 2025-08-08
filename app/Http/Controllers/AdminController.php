<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (!Gate::allows('viewAny', Admin::class)) {
                return $this->responseWithForbidden('You do not have permission to view admins');
            }

            $admins = Admin::paginate(3);

            return (new UserCollection($admins))
                ->additional([
                    'success' => true,
                    'message' => 'Admins retrieved successfully'
                ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve admins', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to retrieve admins: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        try {
            if (!Gate::allows('create', Admin::class)) {
                return $this->responseWithForbidden('You do not have permission to create admins');
            }

            $admin = Admin::create($request->validated());

            return (new UserResource($admin))->additional([
                'success' => true,
                'message' => 'Admin created successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create admin', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to create admin: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        try {
            if (!Gate::allows('view', $admin)) {
                return $this->responseWithForbidden('You do not have permission to view this admin');
            }

            return (new UserResource($admin))->additional([
                'success' => true,
                'message' => 'Admin retrieved successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve admin', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'admin_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to retrieve admin: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        try {
            if (!Gate::allows('update', $admin)) {
                return $this->responseWithForbidden('You do not have permission to update this admin');
            }

            $admin->update($request->validated());

            return (new UserResource($admin))->additional([
                'success' => true,
                'message' => 'Admin updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update admin', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'admin_id' => $admin->id ?? null,
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to update admin: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        try {
            if (!Gate::allows('delete', $admin)) {
                Log::warning('Unauthorized attempt to delete admin', [
                    'user_id' => auth()->id(),
                    'admin_id' => $admin->id
                ]);
                return $this->responseWithForbidden('You do not have permission to delete this admin');
            }

            $adminId = $admin->id;
            $admin->delete();

            Log::info('Admin deleted successfully', [
                'admin_id' => $adminId,
                'deleted_by' => auth()->id()
            ]);

            return $this->responseWithSuccess(null, 'Admin deleted successfully');
        } catch (Exception $e) {
            Log::error('Failed to delete admin', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'admin_id' => $admin->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->responseWithError('Failed to delete admin: ' . $e->getMessage(), 500);
        }
    }
}
