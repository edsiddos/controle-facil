<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransactionRequest;
use App\Services\TransactionService;
use App\Models\Transaction;
use App\Models\AccountCard;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Utiliza a Promoção de Propriedade do PHP 8 para injeção automática de dependência.
     */
    public function __construct(
        protected TransactionService $service
    ) {}

    /**
     * Renderiza a página principal de listagem de transações usando Inertia.js.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Transaction/Index');
    }

    /**
     * Retorna um fluxo JSON paginado para alimentar a WebTable dinâmica.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function webTable(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $limit  = $request->query('limit', 10);
        $offset = $request->query('offset', 0);
        $search = $request->query('search');

        $result = $this->service->listWebTable($userId, (int)$limit, (int)$offset, $search);

        return response()->json($result);
    }

    /**
     * Exibe o formulário de cadastro de uma nova despesa/receita.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $userId = $request->user()->id;

        return Inertia::render('Admin/Transaction/Create', [
            // Retorna apenas contas do próprio usuário logado para popular o select select
            'accountCards' => AccountCard::forUser($userId)->get(['id', 'name']),
            'categories'   => Category::all(['id', 'name'])
        ]);
    }

    /**
     * Salva a nova transação e redireciona com mensagem Flash.
     *
     * @param StoreTransactionRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->service->registerTransaction(
            $request->user()->id,
            $request->validated()
        );

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transação cadastrada com sucesso!');
    }

    /**
     * Exibe o formulário de edição validando se a transação pertence ao usuário via Route Model Binding.
     *
     * @param Request $request
     * @param Transaction $transaction
     * @return Response
     */
    public function edit(Request $request, Transaction $transaction): Response
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        return Inertia::render('Admin/Transaction/Edit', [
            'transaction'  => $transaction,
            'accountCards' => AccountCard::forUser($request->user()->id)->get(['id', 'name']),
            'categories'   => Category::all(['id', 'name'])
        ]);
    }

    /**
     * Atualiza a transação especificada.
     *
     * @param StoreTransactionRequest $request
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $this->service->updateTransaction($transaction->id, $request->user()->id, $request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    /**
     * Remove (Soft Delete) a transação da base de dados.
     *
     * @param Request $request
     * @param Transaction $transaction
     * @return RedirectResponse
     */
    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        try {
            $this->service->removeTransaction($transaction->id, $request->user()->id);

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transação removida com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('transactions.index')
                ->with('error', $e->getMessage());
        }
    }
}
