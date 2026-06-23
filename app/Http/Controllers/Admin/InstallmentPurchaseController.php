<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstallmentPurchaseRequest;
use App\Services\InstallmentPurchaseService;
use App\Models\Category;
use App\Models\AccountCard;
use App\Models\InstallmentPurchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstallmentPurchaseController extends Controller
{

    public function __construct(protected InstallmentPurchaseService $service) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        return Inertia::render('Admin/InstallmentPurchase/Index', [
            'tableData' => $this->service->listForUser($userId)
        ]);
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

    public function create(Request $request): Response
    {
        $userId = $request->user()->id;
        return Inertia::render('Admin/InstallmentPurchase/Create', [
            'categories' => Category::whereNull('user_id')->orWhere('user_id', $userId)->get(),
            'accounts' => AccountCard::where('user_id', $userId)->get()
        ]);
    }

    public function store(StoreInstallmentPurchaseRequest $request): RedirectResponse
    {
        $this->service->storePurchase($request->validated(), Auth::id());
        return redirect()->route('installment-purchases.index')->with('success', 'Compra parcelada cadastrada com sucesso!');
    }

    public function edit(Request $request, InstallmentPurchase $purchase): Response
    {
        // Garante que o usuário só edite o que for dele
        if ($purchase->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        return Inertia::render('Admin/InstallmentPurchase/Edit', [
            'purchase' => $purchase,
            'categories' => Category::whereNull('user_id')->orWhere('user_id', $request->user()->id)->get(),
            'accounts' => AccountCard::where('user_id', $request->user()->id)->get()
        ]);
    }

    public function update(StoreInstallmentPurchaseRequest $request, InstallmentPurchase $purchase): RedirectResponse
    {
        // Garante que o usuário só edite o que for dele
        if ($purchase->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $this->service->updatePurchase($purchase->id, Auth::id(), $request->validated());
        return redirect()->route('installment-purchases.index')->with('success', 'Compra parcelada atualizada com sucesso!');
    }

    public function destroy(Request $request, InstallmentPurchase $purchase): RedirectResponse
    {
        try {
            // Executa a remoção através do Service, garantindo a validação de propriedade (user_id)
            $this->service->deletePurchase($purchase->id, $request->user()->id);

            return redirect()
                ->route('installment-purchases.index')
                ->with('success', 'Parcelamento excluído com sucesso.');
        } catch (\Exception $e) {
            // Caso ocorra alguma exceção, captura e exibe o erro
            return redirect()
                ->route('installment-purchases.index')
                ->with('error', $e->getMessage());
        }
    }
}
