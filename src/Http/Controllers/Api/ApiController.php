<?php

namespace PixiiBomb\Api\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use PixiiBomb\Api\Enums\Action;

/**
 * Base controller for PixiiBomb REST-style API resources.
 *
 * Provides reusable CRUD operations for Eloquent models:
 *  - Get
 *  - GetAll
 *  - Create
 *  - Update
 *  - Patch
 *  - Delete
 *  - DeleteAll
 *
 * Child controllers define:
 *  - Model
 *  - Validator mappings
 */
abstract class ApiController extends Controller
{
    /**
     * Get the model class this controller manages.
     *
     * @return class-string<Model>
     */
    abstract protected function model(): string;

    /**
     * Get the validator class this controller manages.
     *
     * @return class-string
     */
    abstract protected function validator(): string;

    /**
     * Get one record by id.
     */
    public function get(int|string $id): JsonResponse
    {
        $model = $this->model();
        $record = $this->findRecordOrFail($model, $id);

        Gate::authorize('get', $record);

        return response()->json([
            'data' => $record,
        ], 200);
    }

    /**
     * Get all records.
     */
    public function getAll(): JsonResponse
    {
        $model = $this->model();

        Gate::authorize('getAll', $model);

        return response()->json([
            'data' => $model::query()->get(),
        ], 200);
    }

    /**
     * Create a new record.
     */
    public function create(Request $request): JsonResponse
    {
        $model = $this->model();

        Gate::authorize('create', $model);

        $record = $model::query()->create(
            $this->validate($request, Action::CREATE)
        );

        return response()->json([
            'data' => $record,
        ], 201);
    }

    /**
     * Fully update an existing record.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $model = $this->model();
        $record = $this->findRecordOrFail($model, $id);

        Gate::authorize('update', $record);

        $record->update($this->validate($request, Action::UPDATE));

        return response()->json([
            'data' => $record->fresh(),
        ], 200);
    }

    /**
     * Partially update an existing record.
     */
    public function patch(Request $request, int|string $id): JsonResponse
    {
        $model = $this->model();
        $record = $this->findRecordOrFail($model, $id);

        Gate::authorize('update', $record);

        $record->update($this->validate($request, Action::PATCH));

        return response()->json([
            'data' => $record->fresh(),
        ], 200);
    }

    /**
     * Delete an existing record.
     */
    public function delete(int|string $id): JsonResponse
    {
        $model = $this->model();
        $record = $this->findRecordOrFail($model, $id);

        Gate::authorize('delete', $record);

        $record->delete();

        return response()->json([
            'ok' => true,
            'deleted' => $record,
        ], 204);
    }

    /**
     * Delete all records for the managed model.
     */
    public function deleteAll(): JsonResponse
    {
        $model = $this->model();

        Gate::authorize('deleteAll', $model);

        $deleted = $model::query()->delete();

        return response()->json([
            'ok' => true,
            'deleted' => $deleted,
        ]);
    }

    /**
     * Validate the request using the FormRequest mapped to the given action.
     */
    protected function validate(Request $request, Action $action, ?array $only = null): array
    {
        $validation = $this->validator();

        return Validator::make(
            $request->all(),
            $validation::rules($action, $only)
        )->validate();
    }

    protected function findRecordOrFail(string $model, int|string $id): Model
    {
        $record = $model::query()->find($id);

        if (! $record) {
            $table = (new $model)->getTable();

            abort(response()->json([
                'error' => "The `$table` table does not contain a record with id: $id.",
            ], 404));
        }

        return $record;
    }
}
