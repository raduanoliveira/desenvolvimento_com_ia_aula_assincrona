# Etapa 2. Tarefa A (com TDD): filtro por status

Branch: `feature/tdd-guardrail`  
Data: 2026-09-06  
Feature: filtrar a lista ativa por `all`, `pending` ou `done` (`GET /api/tasks?status=`).

## Ciclo Red-Green-Refactor

### Red

- Testes escritos antes da implementação:
  - `tests/Unit/ListTasksServiceTest.php` (pending e done)
  - `tests/Feature/TaskApiTest.php` (filtro e status inválido)
  - `frontend/.../taskApi.test.ts` (query `status=pending`)
  - `frontend/.../TaskStatusFilter.test.tsx`
- Bateria observada (falhas esperadas): 2 unitários, 1 feature, 1 API frontend; o componente de filtro não existia ainda.
- Commit: `49ca1d1` — *Red: testes do filtro de tarefas por status...*

### Green

- Implementação mínima:
  - `ListTasksService::handle($ownerId, $status = 'all')`
  - `ListTasksRequest` com `status` em `all|pending|done`
  - Controller magro passando o status validado
  - `listTasks(status)`, `TaskStatusFilter`, Context e dashboard
- Testes: 58 backend e 28 frontend, todos passando no Docker.
- Commit: *Green: implementa filtro de tarefas por status...*

### Refactor

- Sem refactor estrutural adicional nesta tarefa; a regra ficou no Service e a UI só dispara o filtro.

## Observação

O histórico de commits Red → Green é a evidência principal do ciclo pedido no enunciado.
