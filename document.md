Com base nas premissas de um sistema de controle financeiro robusto, estruturei os requisitos divididos entre **Requisitos Funcionais (RF)** e **Requisitos Não Funcionais (RNF)**.  
Para atender à sua necessidade específica, incluí uma seção dedicada exclusivamente ao controle de **compras a prazo (parceladas)**, garantindo que o impacto no fluxo de caixa futuro e o limite dos cartões sejam gerenciados corretamente.

## **1\. Requisitos Funcionais (RF)**

### **Gestão de Usuários e Acesso**

* **RF01 – Cadastro e Autenticação de Usuários:** O sistema deve permitir que novos usuários criem contas usando e-mail e senha ou via contas sociais (Google, Apple). O acesso deve ser protegido por autenticação.  
* **RF02 – Recuperação de Senha:** Deve oferecer uma opção de "Esqueci minha senha", enviando um link de redefinição para o e-mail cadastrado.  
* **RF03 – Gerenciamento de Perfil:** O usuário deve poder alterar seus dados cadastrais (nome, e-mail, senha) e escolher a moeda padrão (ex: BRL, USD).

### **Controle Financeiro Tradicional**

* **RF04 – Registro de Transações (Receitas e Despesas à Vista):** O usuário deve conseguir inserir, editar e excluir entradas financeiras imediatas. Cada transação deve conter: valor, data, descrição, categoria e a conta associada.  
* **RF05 – Categorização de Gastos:** O sistema deve classificar transações em categorias (Alimentação, Transporte, Lazer, etc.) e permitir a criação de categorias personalizadas.  
* **RF06 – Gestão de Contas e Cartões:** Permite o cadastro de múltiplas contas (Dinheiro em espécie, Conta Corrente) e Cartões de Crédito , controlando saldos e limites de forma individual.  
* **RF07 – Transações Recorrentes (Fixas):** Configuração de despesas ou receitas que se repetem mensalmente de forma automática (ex: assinaturas, salário).

### **💳 Módulo de Compras a Prazo e Parcelamentos (Novo)**

* **RF08 – Registro de Compras Parceladas:** O sistema deve permitir o lançamento de uma despesa informando o **valor total**, a **data da compra**, a **quantidade de parcelas** e o **cartão de crédito ou conta de destino**.  
* **RF09 – Distribuição Automática de Parcelas:** Ao salvar uma compra a prazo, o sistema deve gerar automaticamente as parcelas nos meses subsequentes (ex: Parcela 1/12, Parcela 2/12), associando-as às faturas correspondentes do cartão de crédito.  
* **RF10 – Abatimento de Limite de Crédito:** O sistema deve deduzir o **valor total** da compra do limite disponível do cartão cadastrado e restabelecer esse limite proporcionalmente à medida que as faturas forem marcadas como pagas.  
* **RF11 – Antecipação de Parcelas:** O usuário deve ter a opção de antecipar o pagamento de uma ou mais parcelas futuras, aplicando descontos manualmente se houver, e recalculando o saldo devedor.

### **Planejamento e Visão de Futuro**

* **RF12 – Definição de Orçamentos (Budgets):** O usuário deve poder estabelecer um teto de gastos mensal por categoria. O sistema deve emitir alertas quando o limite estiver próximo.  
* **RF13 – Metas de Economia:** Criação de objetivos financeiros (ex: "Reserva de Emergência"), estipulando um valor final e permitindo aportes.  
* **RF14 – Projeção de Fluxo de Caixa:** O sistema deve exibir uma linha de tendência ou relatório mostrando os gastos fixos e as parcelas futuras já comprometidas para os próximos meses, permitindo que o usuário veja a sua saúde financeira de médio/longo prazo.

### **Relatórios e Notificações**

* **RF15 – Dashboard (Painel Visual):** A tela inicial deve exibir um resumo do mês atual: saldo total, total de receitas/despesas, gráfico de pizza por categoria  e um bloco com a "Fatura Atual do Cartão" detalhando as parcelas daquele mês.  
* **RF16 – Exportação de Dados:** Permite exportar o histórico de transações e compras parceladas em formatos CSV, Excel (XLSX) ou PDF.  
* **RF17 – Notificações e Lembretes:** Envio de alertas (push ou e-mail) sobre faturas de cartão de crédito a vencer e orçamentos que atingiram 80% do limite.

## **2\. Requisitos Não Funcionais (RNF)**

### **Segurança e Privacidade**

* **RNF01 – Criptografia de Dados:** Senhas armazenadas no banco de dados com algoritmos de hash seguros (como BCrypt). Toda a comunicação entre o app e o servidor deve usar criptografia HTTPS (TLS).  
* **RNF02 – Conformidade com a LGPD:** O sistema deve permitir a exclusão definitiva da conta e de todo o histórico financeiro (incluindo o cronograma de parcelas futuras) se o usuário solicitar.

### **Usabilidade e Desempenho**

* **RNF03 – Responsividade:** A interface deve ser adaptável para navegadores (Web) e dispositivos móveis (iOS/Android).  
* **RNF04 – Facilidade de Uso:** O lançamento de uma despesa parcelada ou à vista não deve exigir mais do que 4 cliques/toques a partir da tela inicial para manter o processo ágil.  
* **RNF05 – Tempo de Resposta:** O carregamento do Dashboard e o cálculo de distribuição de parcelas futuras não devem demorar mais do que 2 segundos.  
* **RNF06 – Disponibilidade (Uptime):** O sistema deve estar operacional no mínimo 99,5% do tempo.

## **🚀 Resumo do Escopo Mínimo (MVP)**

Se o objetivo for validar uma primeira versão rápida do produto, o foco deve ser consolidado nos seguintes módulos:

| Módulo | Requisitos Essenciais para o MVP |
| :---- | :---- |
| **Acesso** | Cadastro e Login simples (RF01). |
| **Lançamentos** | Registro de despesas à vista (RF04) , parceladas (RF08) e categorias básicas (RF05). |
| **Visualização** | Painel simples com o saldo atual, gastos do mês e valor da fatura do cartão (RF15). |
| **Segurança** | Criptografia de senhas, HTTPS (RNF01)  e exclusão de dados via LGPD (RNF02). |
