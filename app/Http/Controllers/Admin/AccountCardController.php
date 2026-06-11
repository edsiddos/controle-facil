<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountCardService;
use App\Models\AccountType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountCardController extends Controller
{
    protected AccountCardService $service;

    public function __construct(AccountCardService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        return Inertia::render('Admin/AccountCard', [
            'accounts' => $this->service->listAccounts($userId),
            'accountTypes' => AccountType::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'balance' => 'required|numeric',
            'credit_limit' => 'nullable|numeric|required_if:account_type_id,2', // Supondo ID 2 como Cartão
        ]);

        $this->service->registerAccount($request->user()->id, $validated);

        return redirect()->route('accounts.index')->with('success', 'Conta cadastrada com sucesso!');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->service->removeAccount($id, $request->user()->id);
            return redirect()->route('accounts.index')->with('success', 'Conta removida.');
        } catch (\Exception $e) {
            return redirect()->route('accounts.index')->with('error', $e->getMessage());
        }
    }
}
