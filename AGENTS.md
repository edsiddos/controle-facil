# Documentação Técnica e Arquitetural \- ControleFácil

## 1. Visão Geral do Projeto

O **ControleFácil** é uma aplicação web moderna do tipo Single Page Application (SPA) para gestão de finanças pessoais, com suporte avançado ao controle e projeção de compras parceladas, transações e categorias financeiras.

### Pilares Tecnológicos

* **Back-end:** PHP 8.3+, Laravel 13, Laravel Breeze  
* **Front-end:** Vue.js 3, Inertia.js (Inertia 2.0), Tailwind CSS  
* **Banco de Dados:** PostgreSQL (Produção/Docker), SQLite (Testes)  
* **Infraestrutura:** Docker & Docker Compose  
* **Padrões de Arquitetura:** SOA (Arquitetura Orientada a Serviços), SOLID, Repository Pattern e Form Requests.

---

## 2. Análise Estrutural e Descrição dos Componentes

### 2.1 Camada de Modelos (Domain / Models) \- `app/Models/`

Têm como responsabilidade principal representar a Camada de Domínio e o Mapeamento Objeto-Relacional (ORM) via Eloquent. Em uma arquitetura sob os princípios SOA e SOLID como a deste projeto, os Models são mantidos enxutos (Thin Models). Eles não contêm a lógica de negócio (que fica nos `app/Services`) nem a orquestração de persistência/queries complexas (que fica nos `app/Repositories`).

#### Responsabilidades Principais dos `Models`
* Mapeamento da Tabela e Atributos ($fillable e $casts):
  * Define quais campos do banco de dados podem ser atribuídos em massa (`$fillable`), prevenindo vulnerabilidades de Mass Assignment.
  * Converte automaticamente tipos de dados nativos do banco para tipos do PHP (`$casts`), como transformar datas em instâncias de Carbon e valores monetários em decimais/floats.
* Definição das Entidades de Domínio:
  * Representa as tabelas do sistema (`User`, `AccountType`, `AccountCard`, `Category`, `InstallmentPurchase`, `Transaction`, ...).

#### O Papel dos Scopes (Local Scopes)
Os Scopes no Laravel permitem encapsular condições de consulta reutilizáveis e com nomes expressivos diretamente na model. No código da aplicação, qualquer método com o prefixo `scope` pode ser encadeado nas consultas Eloquent.

* Caso de Uso no Projeto (`scopeForUser`):
  * Para garantir a segurança e o **isolamento multitenant** dos dados, entidades como `InstallmentPurchase`, `Category`, `AccountCard` e `Transaction` possuem o escopo `scopeForUser`.
  * Esse escopo garante que qualquer consulta restrinja os resultados apenas ao `user_id` do usuário logado.

Exemplo no Model (`InstallmentPurchase.php`):
```php
public function scopeForUser(Builder $query, int $userId): Builder
{
    return $query->where('user_id', $userId);
}
```

Uso no Repositório: `InstallmentPurchase::forUser($userId)->get()`;

#### Relacionamentos Eloquent
Os relacionamentos definem como as tabelas do banco de dados se conectam estruturalmente no ORM, permitindo navegar entre entidades de forma orientada a objetos.

1. **`BelongsTo` (Pertence a) - Relação N → 1:** Indica que o registro atual possui uma chave estrangeira (`foreignKey`) apontando para outra tabela principal. No projeto uma compra parcelada (`InstallmentPurchase`) **pertence a** um `User`, a um `AccountCard` e a uma `Category`. 
2. **`HasMany` (Possui Muitos) - Relação 1 → N:** Indica que o registro atual é o pai e possui múltiplos registros filhos vinculados a ele em outra tabela. No projeto uma compra parcelada (`InstallmentPurchase`) **possui muitas** transações/parcelas (`Transaction`).
3. **`HasOne` (Possui Um) - Relação 1 → 1:** Indica que o registro possui exatamente um registro associado em outra tabela. Um exemplo conceitual no projeto seria se o usuário tivesse um perfil estendido de preferências financeiras (`UserProfile`), o model `User` teria uma relação `hasOne(UserProfile::class)`.

### 2.2 Camada de Repositórios (Repository Pattern) \- `app/Repositories/`

Responsável exclusivamente por abstrair e gerenciar o acesso ao banco de dados. Ela serve como uma ponte de comunicação entre a camada de negócios (Services) e o ORM (Eloquent/Models), garantindo que a forma como os dados são consultados ou persistidos fique completamente isolada das regras da aplicação.

#### Principais Responsabilidades
* **Abstração da Camada de Persistência (CRUD e Consultas):** Encapsula todas as operações diretas no banco de dados, tais como criação (create), atualização (update), remoção (delete), buscas por ID e listagens paginadas (paginateForUser).
* Encapsulamento de Filtros e Carregamento de Relacionamentos: Isola a construção das consultas Eloquent, aplicando filtros de ordenação (orderBy), pesquisas e carregamento ansioso de relacionamentos.
* Garantia de Isolamento dos Dados do Usuário (Multitenancy): Aplica os escopos de usuário (scopeForUser) nas consultas para garantir que a Service e o Controller só recebam registros pertencentes ao usuário autenticado.
* Inversão de Dependência (Princípio "D" do SOLID): Desacopla as regras de negócio da ferramenta de banco de dados. As Services dependem de interfaces/contratos e não das implementações concretas do ORM.

#### Estrutura dos Repositories no Projeto
* A camada é dividida em duas subpastas principais:
  * app/Repositories/Contracts/ (Interfaces):
    * Definem o contrato/assinatura dos métodos que o repositório deve ter (ex: InstallmentPurchaseRepositoryInterface).
    * Especificam o que a aplicação precisa fazer com os dados (ex: paginateForUser, create, findForUser, delete), sem expor código do Laravel ou SQL.
  * app/Repositories/Eloquent/ (Implementações Concretas):
    * Implementam as interfaces utilizando a sintaxe do ORM Eloquent (ex: InstallmentPurchaseRepository).
    * É o único lugar do fluxo de negócio onde a sintaxe direta do Eloquent/Model é executada.
* Injeção no Service Container (AppServiceProvider): No AppServiceProvider.php, cada interface é vinculada à sua classe concreta ($this->app->bind(...)). Se no futuro o banco de dados ou ORM mudar, basta criar uma nova implementação e alterar o binding no Provider, sem modificar uma linha da camada de Service ou dos Controllers.

### 2.3 Camada de Serviços (Business Logic / Service Layer) \- `app/Services/`

Atua como a Camada de Regras de Negócio e Lógica da Aplicação (SOA/SOLID). Sua principal responsabilidade é isolar toda a inteligência e orquestração de dados da aplicação, garantindo que os Controllers permaneçam enxutos (thin controllers) e desacoplados da camada de persistência.

#### Principais Responsabilidades
* **Centralizar a Regra de Negócio**: Processa cálculos complexos, validações de domínio e regras específicas de finanças antes de salvar ou manipular os dados.
* **Orquestrar Transações de Banco de Dados (`DB::transaction`)**: Garante a atomicidade das operações. Se uma ação envolver múltiplos registros (como criar uma compra e gerar 12 parcelas de uma só vez), a Service assegura que tudo seja salvo ou revertido (rollback) em caso de falha.
* **Intermediar Controllers e Repositórios**: Os Controllers recebem as requisições HTTP e apenas delegam as ações para a Service, que por sua vez se comunica com a camada de Repositórios (`app/Repositories/`) para consultar ou persistir dados no banco.
* **Formatar Dados para a Interface (`WebTable`)**: Prepara a estrutura e os filtros das listagens paginadas antes de enviar as respostas para a tabela dinâmica do front-end Vue.js.

### 2.4 Camada de Validação (Requests) \- `app/Http/Requests/Admin/`

Os **Form Requests** (`app/Http/Requests/Admin/`) têm como responsabilidade principal atuar como a **Camada de Validação, Sanitização e Autorização das Requisições HTTP**. Eles interceptam a requisição assim que ela chega ao back-end, garantindo que apenas dados válidos, seguros e bem-formatados alcancem os Controllers e a camada de negócio (`Services`).

#### Principais Responsabilidades
* **Definição de Regras de Validação (`rules()`):** Especifica com precisão os critérios que cada parâmetro do formulário deve atender (presença obrigatória, tipos de dados, limites mínimos e máximos, formato de data, e verificação de existência no banco de dados via `exists`).
* **Mensagens de Erro Amigáveis (`messages()`):** Fornece mensagens de validação personalizadas e traduzidas em português para cada falha de regra, enviando um feedback claro de erro para ser exibido nos componentes Vue.js (InputError.vue).
* **Controle de Autorização (`authorize()`):** Valida se o usuário logado tem permissão para realizar aquela operação antes mesmo de rodar as regras de validação.
* **Extração Segura de Dados (`validated()`):** Disponibiliza apenas os campos devidamente validados e autorizados através do método `$request->validated()`, impedindo ataques de injeção de parâmetros indesejados.
* **Aplicação do Princípio de Responsabilidade Única (SRP / SOLID):** Desacopla completamente a lógica de validação do Controller e da Service, evitando `if/else` poluídos no fluxo principal do sistema.

### 2.5 Camada de Controladores (Controllers) \- `app/Http/Controllers/Admin/`

Os **Controllers** (`app/Http/Controllers/Admin/`) têm como responsabilidade principal atuar como **Orquestradores da Camada HTTP e Adaptadores de Resposta para o Front-end**. Seguindo a arquitetura SOA/SOLID adotada, os controllers do projeto são mantidos **extremamente enxutos (Thin Controllers)**. Eles não contêm lógica de validação (que fica nos Form Requests), lógica de negócio (que fica nos Services) nem consultas de banco de dados (que ficam nos Repositories).

#### Principais Responsabilidades
* **Receber a Requisição HTTP:** Recebem as requisições web enviadas pelo front-end Vue 3 / Inertia.js, já tratadas e validadas pelos Form Requests.
* **Injeção de Dependência dos Serviços:** Utilizam a **Promoção de Propriedades do PHP 8** no construtor para injetar as classes de serviço necessárias (`InstallmentPurchaseService`, `AccountCardService`, `CategoryService`).
* **Delegar a Execução para a Camada de Serviço (`Service`):** Repassam os dados validados (`$request->validated()`) e o ID do usuário autenticado (`$request->user()->id`) diretamente para os métodos da Service, sem processar regras internamente.
* **Retornar Respostas Formatadas para o Front-end:**
  * **Inertia Views (`Inertia::render`):** Renderizam as telas da SPA (ex: `Index`, `Create`, `Edit`) injetando as props necessárias do Vue.
  * **Respostas JSON:** Retornam dados paginados formatados em JSON para componentes dinâmicos, como o endpoint webTable.
  * **Redirecionamentos com Flash Messages:** Redirecionam o usuário após ações de mutação (`store`, `update`, `destroy`) enviando mensagens de sucesso ou alerta (`with('success', ...)`).   

### 2.6 Camada Visual e Interface (Front-end Vue 3 / Inertia) \- `resources/js/`

A camada visual e de interface baseada em **Vue 3** e **Inertia.js** desempenha um papel fundamental na experiência do usuário e na arquitetura do sistema.

#### Importância da Camada Visual (Vue 3 / Inertia.js)
* SPA Monolítica sem a Complexidade de uma API Separada:
  * O Inertia.js permite criar uma Single Page Application (SPA) moderna sem a necessidade de construir uma API REST/GraphQL completa nem gerenciar estado de autenticação via tokens JWT no front-end.
  * O roteamento, validação e controle de acesso permanecem no Laravel, enquanto o front-end reage como um aplicativo de página única fluido e sem recarregamentos de página (page refreshes).

* Reatividade e Produtividade com Vue 3 (Composition API / `<script setup>`):
  * O Vue 3 garante que as alterações de dados (saldos, inserção de transações, parcelamentos) reflitam instantaneamente na interface.
  * O uso do hook `useForm` do Inertia sincroniza automaticamente os estados dos formulários, requisições HTTP, estados de carregamento (`form.processing`) e mensagens de validação do Laravel.

* Consistência Visual e Reutilização de Código:
  * Integrada ao **Tailwind CSS**, a interface mantém um design responsivo, padronizado e limpo em todas as telas da aplicação.

#### Descrição das Pastas e Componentes Front-End

1. `components/ (resources/js/Components/)`
  * **O que é:** A biblioteca de componentes atômicos e genéricos de UI (User Interface) do projeto.
  * **Responsabilidade:** Fornecer blocos de construção reutilizáveis e desacoplados, garantindo acessibilidade e padrão visual único em todo o sistema.
  * **Principais Componentes do Projeto:**
    * `WebTable.vue:` Componente central para exibição de listagens de dados. Consome diretamente endpoints em JSON (`webtable`) do back-end, oferecendo paginação reativa e slots customizáveis para ações (botões de edição, exclusão, etc.).
    * `TextInput.vue` & `SelectInput.vue`: Campos de entrada de texto, números e seleções com estilos padronizados do Tailwind.
    * `InputLabel.vue` & `InputError.vue`: Rótulos e exibições automáticas das mensagens de erro retornadas pelos Form Requests do Laravel.
    * `PrimaryButton.vue` & `DangerButton.vue`: Botões com estilos visuais para ações primárias (salvar) e destrutivas (excluir).
    * `Modal.vue`: Janelas sobrepostas para confirmações de exclusão e diálogos com o usuário.

2. layouts/ (resources/js/Layouts/)
  * **O que é:** As molduras ou templates estruturais da aplicação.
  * **Responsabilidade:** Definir o esqueleto visual reutilizável que envolve o conteúdo das páginas, mantendo elementos persistentes (como cabeçalhos, barras de navegação, sidebars e menus de perfil) sem reconstruí-los a cada navegação.
  * **Layouts do Projeto:**
    * `AuthenticatedLayout.vue`: Layout da área protegida/administrativa. Contém o menu superior/lateral de navegação, dados do usuário logado, contêiner com slot principal (`<slot />`) e slot para o cabeçalho da página (`#header`).
    * `GuestLayout.vue`: Layout simplificado para telas públicas ou não autenticadas (como telas de Login, Registro e Redefinição de Senha).

3. pages/admin/ (resources/js/Pages/Admin/)
  * **O que é:** As telas/páginas completas do sistema, organizadas por módulo dentro da área administrativa.
  * **Responsabilidade:** Representar as visões finais renderizadas pelo método `Inertia::render()` dos Controllers do Laravel. Cada página orquestra os componentes genéricos (`Components/`), envolve-se no layout adequado (`Layouts/`) e gerencia os dados enviados pelo back-end via props.
  * Estrutura Interna por Módulo (ex: `InstallmentPurchase/`):
    * `Index.vue`: Tela principal do recurso. Exibe o título da página, botão para novo cadastro e a tabela dinâmica `<WebTable/>` integrada ao endpoint do controller.
    * `Form.vue`: Componente de formulário reutilizado tanto no cadastro quanto na edição. Contém os inputs, regras do `useForm` e tratamento das props (`cards`, `categories`).
    * `Create.vue`: Página que encapsula o `Form.vue` dentro do `AuthenticatedLayout.vue` para a criação de novos registros.
    * `Edit.vue`: Página que carrega os dados de um registro existente e passa para o `Form.vue` em modo de edição.

---

## 3. Passo a Passo para Implementar uma Nova Funcionalidade

1. **Migration no Banco de Dados:** Criar a migração definindo os campos no esquema da tabela.  
2. **Model Eloquent:** Configurar `$fillable`, relacionamentos e o escopo `scopeForUser`.  
3. **Repository Pattern:** Definir a interface em `Contracts/` e a classe concreta em `Eloquent/`, vinculando-as no `AppServiceProvider`.
4. **Service Provider:** Registrar o binding da interface do repositório com a implementação Eloquent.
5. **Camada de Serviço (`Service`):** Implementar as regras de negócio e métodos de suporte para a `WebTable`.
6. **Form Request:** Escrever as regras de validação e mensagens amigáveis em português.  
7. **Controller Administrativo:** Injetar o Service, gerenciar o CRUD e retornar respostas `Inertia::render` ou `JsonResponse`.  
8. **Rotas HTTP:** Mapear as rotas protegidas pelo middleware `auth` em `routes/web.php`.  
9. **Front-end Vue 3 \+ Inertia:** Desenvolver a interface com `Index.vue` (WebTable), `Form.vue`, `Create.vue` e `Edit.vue`. 
10. **Testes Automatizados:** Escrever os testes de Feature em `tests/Feature/Admin/`.

