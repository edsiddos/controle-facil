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
     * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return Response Renderização da view via Inertia.js.
     */
    public function index(Request $request): Response
    {
        // Recupera o ID do usuário atualmente autenticado na sessão
        $userId = $request->user()->id;

        // Renderiza o componente Vue/React em 'resources/js/Pages/Admin/AccountCard/Index.vue'
        return Inertia::render('Admin/AccountCard/Index');
    }

    /**
     * Exemplo de endpoint para alimentar a WebTable dinamicamente.
     */
    public function webTable(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user()->id;

        // Captura os parâmetros da URL/Request com valores padrão de segurança
        $limit  = $request->query('limit', 10);    // Padrão: 10 registros por página
        $offset = $request->query('offset', 0);    // Padrão: Começa do início
        $search = $request->query('search');       // nulo se não enviado

        // Chama o método estruturado no Service
        $result = $this->service->listWebTable($userId, (int)$limit, (int)$offset, $search);

        // Retorna como JSON para sua tabela reativa no Vue/React
        return response()->json($result);
    }

    /**
     * Exibe a tela de criação de uma nova conta/cartão.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/AccountCard/Create', [
            'accountTypes' => AccountType::all(['id', 'name']),
            'typeCreditCard' => AccountCard::TYPE_CREDIT_CARD
        ]);
    }

    /**
     * Cadastra uma nova conta ou cartão no sistema.
     * @param StoreAccountCardRequest $request Request customizado que valida os dados de entrada.
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
     * Exibe a tela de edição de uma conta/cartão existente.
     * @param Request $request Objeto da requisição.
     * @param AccountCard $accountCard Uso de Route Model Binding: o Laravel busca o model no banco automaticamente usando o ID da URL.
     * @return RedirectResponse Redirecionamento HTTP com feedback de sucesso ou erro.
     */
    public function edit(Request $request, AccountCard $accountCard): Response
    {
        // Garante que o usuário só edite o que for dele
        if ($accountCard->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        return Inertia::render('Admin/AccountCard/Edit', [
            'account' => $accountCard,
            'accountTypes' => AccountType::all(['id', 'name']),
            'typeCreditCard' => AccountCard::TYPE_CREDIT_CARD
        ]);
    }

    /**
     * Atualiza os dados da conta/cartão.
     * @param StoreAccountCardRequest $request Objeto da requisição.
     * @param AccountCard $accountCard Uso de Route Model Binding: o Laravel busca o model no banco automaticamente usando o ID da URL.
     * @return RedirectResponse Redirecionamento HTTP com feedback de sucesso ou erro.
     */
    public function update(StoreAccountCardRequest $request, AccountCard $accountCard): RedirectResponse
    {
        if ($accountCard->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado.');
        }

        $this->service->updateAccount($accountCard->id, $request->user()->id, $request->validated());

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Cartão/Conta atualizado com sucesso!');
    }

    /**
     * Remove uma conta ou cartão específico.
     * @param Request $request Objeto da requisição.
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
