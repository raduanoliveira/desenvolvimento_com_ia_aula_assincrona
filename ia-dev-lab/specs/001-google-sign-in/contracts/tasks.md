# Contract: tarefas com dono

Mesmos verbos e paths de hoje. Mudança: **sessão obrigatória** e **isolamento por dono**. Cookie de sessão em todos os pedidos (`credentials: include`). Recurso JSON inalterado (`id`, `title`, `done`, `archived`). `user_id` **não** vai no JSON da tarefa (a tela não precisa; o dono é a sessão).

Autenticação em todas as rotas abaixo: sem sessão → `401` `{"message":"Não autenticado."}` e **nenhum** array de tarefas.

Tarefa de outro dono ou órfã (`user_id` nulo): tratada como inexistente para quem pediu → `404`.

## GET `/api/tasks`

Lista ativa **só** do dono da sessão (`archived = false`).

- `200` `{ "data": [ Task, ... ] }`
- Primeira visita: `data` vazio; **não** inclui órfãs globais

## POST `/api/tasks`

Cria tarefa com `user_id` da sessão.

- Body: `{ "title": "Pagar conta" }`
- `201` `{ "data": Task }`
- Título vazio: `422` (regra já existente)

## PATCH `/api/tasks/{task}/toggle`

- Dona da tarefa: `200` `{ "data": Task }` com `done` invertido
- Outra pessoa / órfã / id inexistente: `404`
- Sem sessão: `401`

## PATCH `/api/tasks/{task}/archive`

- Dona: `200` `{ "data": Task }` com `archived: true`; some da listagem ativa
- Outra pessoa / órfã / id inexistente: `404`
- Sem sessão: `401`

## DELETE `/api/tasks/{task}`

- Dona: `204`
- Outra pessoa / órfã / id inexistente: `404`
- Sem sessão: `401`

## Isolamento (contrato de aceite)

Dado User A com tarefa “Pagar conta” e User B com sessão própria:

- `GET /api/tasks` como B **não** contém “Pagar conta”
- `PATCH`/`DELETE` no id da tarefa de A, como B → `404`; a linha de A permanece no banco
