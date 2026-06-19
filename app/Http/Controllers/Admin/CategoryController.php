<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Services\CategoryService;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $service
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        return Inertia::render('Admin/Category/Index', [
            'categories' => $this->service->listCategories($userId)
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Category/Create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->service->registerCategory($request->user()->id, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria cadastrada com sucesso!');
    }

    public function edit(Request $request, Category $category): Response
    {
        if ($category->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado ou categoria global não editável.');
        }

        return Inertia::render('Admin/Category/Edit', [
            'category' => $category
        ]);
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        try {
            $this->service->updateCategory($category->id, $request->user()->id, $request->validated());
            return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        try {
            $this->service->removeCategory($category->id, $request->user()->id);
            return redirect()->route('categories.index')->with('success', 'Categoria removida com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }

    public function webTable(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user()->id;
        $limit = (int) $request->query('limit', 10);
        $offset = (int) $request->query('offset', 0);
        $search = $request->query('search');

        $result = $this->service->listWebTable($userId, $limit, $offset, $search);

        return response()->json($result);
    }
}
