# 💰 ControleFácil

![Contador de Acessos](https://komarev.com/ghpvc/?username=edsiddos&repo=controle-facil&color=orange&style=flat-square)

O **ControleFácil** é uma aplicação web moderna voltada para a gestão e controle de gastos pessoais. O grande diferencial do sistema é a inteligência e flexibilidade para a inserção e projeção de compras parceladas, permitindo uma visão clara do fluxo financeiro futuro.

---

## 🚀 Diferenciais Técnicos & Arquitetura

Este projeto foi desenvolvido utilizando as melhores práticas de engenharia de software para demonstrar alta manutenibilidade, escalabilidade e código limpo:

* **Arquitetura Orientada a Serviços (SOA):** Separação estrita de regras de negócio complexas (como o cálculo e o desmembramento das parcelas futuras) em camadas de serviços isoladas, mantendo os *Controllers* enxutos e focados apenas no fluxo de requisição.
* **Princípios SOLID:** Aplicação prática de design patterns e inversão de dependência através do uso de interfaces, facilitando a testabilidade e futuras trocas de provedores de serviços.
* **Experiência SPA com Vue.js & Inertia.js:** Integração do ecossistema do Laravel 13 (via Breeze) com o Vue.js, criando uma interface reativa, fluida e com excelente experiência de usuário, sem a necessidade de construir e gerenciar uma API REST separada.
* **Persistência Robusta com PostgreSQL:** Modelagem de dados e integridade referencial preparadas para consistência financeira, com o banco de dados que utilizo amplamente em ambiente de produção no meu dia a dia.
* **Ambiente Conteinerizado (Docker):** Configuração completa via `docker-compose`, isolando os ambientes de aplicação e banco de dados para garantir que o projeto rode perfeitamente em qualquer máquina.
 
---

## 🛠️ Tecnologias Utilizadas

* **Back-end:** PHP, Laravel 13 (recursos mais recentes do framework)
* **Front-end:** Vue.js, Inertia.js, Tailwind CSS (via Laravel Breeze)
* **Banco de Dados:** PostgreSQL
* **Infraestrutura:** Docker & Docker Compose

<!--
---

## ⚙️ Como Rodar o Projeto Localmente

Certifique-se de ter o **Docker** e o **Docker Compose** instalados em sua máquina.

### 1. Clonar o repositório
```bash
git clone [https://github.com/seu-usuario/controlefacil.git](https://github.com/seu-usuario/controlefacil.git)
cd controlefacil
 -->
