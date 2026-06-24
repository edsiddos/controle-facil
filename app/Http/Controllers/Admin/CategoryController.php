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

/**
 * Classe CategoryController
 * Controlador responsável por mediar as requisições HTTP da área administrativa relativas a categorias.
 */
class CategoryController extends Controller
{
    /**
     * Construtor da classe.
     * @param CategoryService $service Instância da camada de serviço injetada automaticamente pelo Laravel.
     */
    public function __construct(
        protected CategoryService $service
    ) {}

    /**
     * Exibe a página principal de listagem de categorias utilizando Inertia.
     * @param Request $request Instância da requisição HTTP atual.
     * @return Response Retorna a renderização da view Inertia 'Admin/Category/Index'.
     */
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        return Inertia::render('Admin/Category/Index');
    }

    /**
     * Exibe o formulário de criação de uma nova categoria.
     * @return Response Retorna a renderização da view Inertia 'Admin/Category/Create'.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Category/Create');
    }

    /**
     * Processa a criação e persistência de uma nova categoria.
     * @param StoreCategoryRequest $request Requisição customizada com as regras de validação dos dados.
     * @return RedirectResponse Redireciona de volta para a index com uma mensagem de sucesso no flash session.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->service->registerCategory($request->user()->id, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoria cadastrada com sucesso!');
    }

    /**
     * Exibe o formulário de edição para uma categoria existente, validando a posse.
     * @param Request $request Instância da requisição HTTP atual.
     * @param Category $category Modelo do tipo Category injetado via Route Model Binding.
     * @return Response Retorna a renderização da view Inertia 'Admin/Category/Edit'.
     */
    public function edit(Request $request, Category $category): Response
    {
        if ($category->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado ou categoria global não editável.');
        }

        return Inertia::render('Admin/Category/Edit', [
            'category' => $category
        ]);
    }

    /**
     * Processa a atualização de uma categoria com tratamento de possíveis exceções de negócio.
     * @param StoreCategoryRequest $request Requisição contendo os dados validados.
     * @param Category $category Modelo injetado que está sendo atualizado.
     * @return RedirectResponse Redireciona com feedback de sucesso ou erro obtido na camada de serviço.
     */
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

    /**
     * Remove logicamente (soft delete) uma categoria com validação do usuário proprietário.
     * @param Request $request Instância da requisição HTTP atual.
     * @param Category $category Modelo injetado correspondente à categoria a ser excluída.
     * @return RedirectResponse Redireciona indicando o resultado do processo (sucesso ou falha).
     */
    public function destroy(Request $request, Category $category): RedirectResponse
    {
        try {
            $this->service->removeCategory($category->id, $request->user()->id);
            return redirect()->route('categories.index')->with('success', 'Categoria removida com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Fornece dados em formato JSON para tabelas assíncronas (como datatables no front-end).
     * @param Request $request Instância da requisição contendo parâmetros de paginação e filtros na query string.
     * @return \Illuminate\Http\JsonResponse Retorna um objeto JSON contendo os dados estruturados obtidos do serviço.
     */
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