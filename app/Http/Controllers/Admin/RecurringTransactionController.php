<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RecurringTransactionService;
use App\Models\RecurringTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class RecurringTransactionController extends Controller
{
    /**
     * Utiliza a Promoção de Propriedade do PHP 8 para injeção automática de dependência.
     */
    public function __construct(
        protected RecurringTransactionService $service
    ) {}

    /**
     * Renderiza a página principal de listagem de assinaturas/planos recorrentes usando Inertia.js.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/RecurringTransaction/Index');
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
     * Informa que a assinatura/plano foi cancelado e remove as transações futuras.
     *
     * @param Request $request
     * @param RecurringTransaction $recurringTransaction
     * @return RedirectResponse
     */
    public function cancel(Request $request, RecurringTransaction $recurringTransaction): RedirectResponse
    {
        if ($recurringTransaction->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        try {
            $this->service->cancelRecurringTransaction($recurringTransaction->id, $request->user()->id);

            return redirect()
                ->route('recurring-transactions.index')
                ->with('success', 'Assinatura/plano cancelado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('recurring-transactions.index')
                ->with('error', $e->getMessage());
        }
    }
}
