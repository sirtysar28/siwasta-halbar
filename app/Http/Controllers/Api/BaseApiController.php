<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller
{
    protected string $model;
    protected array $searchable = [];
    protected array $rules = [];
    protected array $with = [];

    public function index(Request $request)
    {
        $query = ($this->model)::query()->with($this->with);

        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                foreach ($this->searchable as $col) {
                    $q->orWhere($col, 'like', "%{$s}%");
                }
            });
        }
        if ($request->filled('per_page')) {
            return $this->success($query->paginate((int)$request->per_page));
        }
        return $this->success($query->get());
    }

    public function show($id)
    {
        $item = ($this->model)::with($this->with)->findOrFail($id);
        return $this->success($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $item = ($this->model)::create($data);
        return $this->success($item, 'Data berhasil dibuat', 201);
    }

    public function update(Request $request, $id)
    {
        $item = ($this->model)::findOrFail($id);
        $data = $request->validate($this->rules);
        $item->update($data);
        return $this->success($item, 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        ($this->model)::findOrFail($id)->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }

    protected function success($data, string $message = 'OK', int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message, int $code = 400, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
