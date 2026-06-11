<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccountCardRequest;
use App\Services\AccountCardService;
use App\Models\AccountCard;
use App\Models\AccountType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountCardController extends Controller
{
    /**
     * O construtor utiliza a "Promoção de Propriedade" do PHP 8+.
     * Isso declara e injeta automaticamente o serviço de regras de negócio (Service Pattern).
     */
    public function __construct(
        protected AccountCardService $service
    ) {}

    /**
     * Exibe a listagem de contas e cartões do administrador.
     * * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return Response Renderização da view via Inertia.js.
     */
    public function index(Request $request): Response
    {
        // Recupera o ID do usuário atualmente autenticado na sessão
        $userId = $request->user()->id;

        // Renderiza o componente Vue/React em 'resources/js/Pages/Admin/AccountCard'
        return Inertia::render('Admin/AccountCard', [
            // Busca as contas associadas ao usuário através da camada de serviço
            'accounts' => $this->service->listAccounts($userId),

            // Retorna apenas as colunas necessárias dos tipos de conta para otimizar o payload
            'accountTypes' => AccountType::all(['id', 'name'])
        ]);
    }

    /**
     * Cadastra uma nova conta ou cartão no sistema.
     * * @param StoreAccountCardRequest $request Request customizado que valida os dados de entrada.
     * @return RedirectResponse Redirecionamento HTTP com mensagem de sucesso.
     */
    public function store(StoreAccountCardRequest $request): RedirectResponse
    {
        // $request->validated() retorna apenas os campos que passaram com sucesso na validação do Request
        $this->service->registerAccount(
            $request->user()->id,
            $request->validated()
        );

        // Redireciona o usuário de volta para a rota de listagem injetando uma mensagem flash na sessão
        return redirect()
            ->route('accounts.index')
            ->with('success', 'Cartão/Conta cadastrado com sucesso!');
    }

    /**
     * Remove uma conta ou cartão específico.
     * * @param Request $request Objeto da requisição.
     * @param AccountCard $accountCard Uso de Route Model Binding: o Laravel busca o model no banco automaticamente usando o ID da URL.
     * @return RedirectResponse Redirecionamento HTTP com feedback de sucesso ou erro.
     */
    public function destroy(Request $request, AccountCard $accountCard): RedirectResponse
    {
        try {
            // Executa a remoção através do Service, garantindo a validação de propriedade (user_id)
            $this->service->removeAccount($accountCard->id, $request->user()->id);

            return redirect()
                ->route('accounts.index')
                ->with('success', 'Cartão/Conta removido.');
        } catch (\Exception $e) {
            // Caso ocorra alguma exceção (ex: conta não pertence ao usuário ou erro de banco), captura e exibe o erro
            return redirect()
                ->route('accounts.index')
                ->with('error', $e->getMessage());
        }
    }
}
