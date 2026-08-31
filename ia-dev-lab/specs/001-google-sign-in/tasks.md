---
description: "Task list for entrar com Google"
---

# Tasks: Entrar com Google

**Input**: Design documents from `/specs/001-google-sign-in/`

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md

**Tests**: TDD obrigatório (constituição, plano e pedido desta geração). Tarefas de teste vêm **antes** da implementação correspondente; o teste deve falhar antes do código de produção.

**Organization**: Identidade/sessão/`users` primeiro. **PARAR no T030** (checkpoint humano) **antes** de qualquer migration `user_id` em `tasks`. Só então isolamento da API e, por último, a UI.

**Ordem de execução (não é a ordem P1 da spec)**: Fases 1–2 → **T030** → Fase 3 (dono) → US4 → US3 → US7 → US2 → US1 → US5 → US6 → polish.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

Monorepo: `backend/` (Laravel) e `frontend/` (React). Comandos só via `docker compose` na pasta do laboratório.

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Dependências e configuração de ambiente no stack já existente. Sem tabela `users` ainda. **Proibido** criar migration de `user_id` em `tasks`.

- [x] T001 Install `laravel/socialite` with `docker compose exec backend composer require laravel/socialite` (lock in `backend/composer.json` and `backend/composer.lock`)
- [x] T002 [P] Add empty `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI` to `backend/.env.example` (keep `FRONTEND_URL`; never put real secrets)
- [x] T003 [P] Create Google Socialite entries from env in `backend/config/services.php`
- [x] T004 [P] Set `supports_credentials` to true in `backend/config/cors.php`
- [x] T005 Enable Laravel session cookies on API routes and JSON 401 for guests in `backend/bootstrap/app.php`

---

## Phase 2: Foundational — identidade, sessão e users

**Purpose**: Pessoa Google, sessão e `/api/session`. **MUST** terminar no checkpoint T030. Nenhuma story de tarefa ou UI começa antes. **Proibido** `user_id` em `tasks`.

**⚠️ CRITICAL**: T030 bloqueia a Fase 3 e todas as user stories

- [x] T006 Create `IdentityUser` DTO and `IdentityProviderInterface` in `backend/app/Identity/IdentityUser.php` and `backend/app/Identity/IdentityProviderInterface.php`
- [x] T007 [P] Implement `FakeIdentityProvider` (success, cancelled, error) in `backend/app/Identity/FakeIdentityProvider.php`
- [x] T008 [P] Implement Socialite adapter in `backend/app/Identity/SocialiteIdentityProvider.php`
- [x] T009 [P] Add dummy `GOOGLE_*` env values to `backend/phpunit.xml` so tests never need real Google secrets
- [x] T010 Create `users` table migration (`id`, unique `google_id`, `name`, `email`, timestamps) in `backend/database/migrations/2026_08_31_120000_create_users_table.php`
- [x] T011 [P] Create Authenticatable `User` model in `backend/app/Models/User.php`
- [x] T012 [P] Create `UserFactory` in `backend/database/factories/UserFactory.php`
- [x] T013 Create `UserRepositoryInterface` (`findByGoogleId`, `create`) in `backend/app/Repositories/Contracts/UserRepositoryInterface.php`
- [x] T014 Implement `EloquentUserRepository` in `backend/app/Repositories/EloquentUserRepository.php`
- [x] T015 [P] Point the users provider at `App\Models\User` in `backend/config/auth.php` (create the file if missing)
- [x] T016 Bind `UserRepositoryInterface` and `IdentityProviderInterface` (Socialite in app, Fake in tests) in `backend/app/Providers/AppServiceProvider.php` and `backend/tests/TestCase.php`
- [x] T017 Write failing unit test: find-or-create by `google_id` and never touch tasks in `backend/tests/Unit/CompleteGoogleSignInServiceTest.php`
- [x] T018 Implement `CompleteGoogleSignInService` in `backend/app/Services/CompleteGoogleSignInService.php`
- [x] T019 Write failing feature tests for `GET /api/session` (401 without session, 200 with name/email) in `backend/tests/Feature/SessionApiTest.php`
- [x] T020 Implement `UserResource` and `SessionController@show` plus `GET /api/session` in `backend/app/Http/Resources/UserResource.php`, `backend/app/Http/Controllers/Api/SessionController.php`, and `backend/routes/api.php`
- [x] T021 Write failing feature test for `DELETE /api/session` returning 204 in `backend/tests/Feature/SessionApiTest.php`
- [x] T022 Implement `EndSessionService` and `SessionController@destroy` in `backend/app/Services/EndSessionService.php` and `backend/app/Http/Controllers/Api/SessionController.php`
- [x] T023 Write failing feature test that `GET /auth/google` redirects using the fake provider in `backend/tests/Feature/GoogleAuthTest.php`
- [x] T024 Implement start action and `GET /auth/google` in `backend/app/Http/Controllers/Auth/GoogleAuthController.php` and `backend/routes/web.php`
- [x] T025 Write failing feature test: successful callback creates `User`, opens session, redirects to `FRONTEND_URL` in `backend/tests/Feature/GoogleAuthTest.php`
- [x] T026 Implement successful callback (HTTP only: provider → `CompleteGoogleSignInService` → `Auth::login` → redirect) in `backend/app/Http/Controllers/Auth/GoogleAuthController.php`
- [x] T027 Write failing feature tests: cancelled → `{FRONTEND_URL}/?signin=cancelled`; error → `/?signin=error`; no session in `backend/tests/Feature/GoogleAuthTest.php`
- [x] T028 Handle cancel and provider error in `backend/app/Http/Controllers/Auth/GoogleAuthController.php` without secrets in the redirect
- [x] T029 Run `docker compose exec backend php artisan test --testsuite=Unit` and `--testsuite=Feature` confirming `backend/tests/Feature/GoogleAuthTest.php`, `backend/tests/Feature/SessionApiTest.php`, and existing `backend/tests/Feature/TaskApiTest.php` still pass (list still global)

**Checkpoint T030 — humano, obrigatório**

- [x] T030 **STOP**: human reviews orphan-task impact using `specs/001-google-sign-in/quickstart.md` and `specs/001-google-sign-in/data-model.md`; approve nullable `user_id`, no backfill, no `NOT NULL`. Do **not** create `*_add_user_id_to_tasks_table.php` until this box is checked by a person

---

## Phase 3: Dono nas tarefas (pós-checkpoint, bloqueia stories de lista)

**Purpose**: Schema e repositório por dono. Só depois de T030. Ainda sem mudar a UI.

- [x] T031 Create nullable `user_id` FK migration **without backfill** in `backend/database/migrations/2026_08_31_130000_add_user_id_to_tasks_table.php`
- [x] T032 [P] Add `user_id` (nullable, fillable) and `user()` relation on `backend/app/Models/Task.php`
- [x] T033 [P] Allow optional `user_id` in `backend/database/factories/TaskFactory.php`
- [x] T034 Add `allForUser` and `findForUser` to `backend/app/Repositories/Contracts/TaskRepositoryInterface.php`
- [x] T035 Implement `allForUser` / `findForUser` (exclude null `user_id`) in `backend/app/Repositories/EloquentTaskRepository.php`

---

## Phase 4: User Story 4 - Pedido sem sessão é recusado (Priority: P2)

**Goal**: Listar/criar/concluir/arquivar/excluir sem sessão devolve 401 e nenhuma tarefa.

**Independent Test**: `getJson`/`postJson` em `/api/tasks` sem `actingAs` → 401 e sem array de tarefas.

### Tests for User Story 4

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T036 [US4] Write failing feature tests for 401 on GET/POST/PATCH/DELETE `/api/tasks` without session in `backend/tests/Feature/TaskUnauthenticatedApiTest.php`

### Implementation for User Story 4

- [x] T037 [US4] Require authenticated session on task routes in `backend/routes/api.php`
- [x] T038 [US4] Update existing cases in `backend/tests/Feature/TaskApiTest.php` to `actingAs` a `User` so authenticated happy-paths still pass

**Checkpoint**: Pedido anônimo à API de tarefas é recusado

---

## Phase 5: User Story 3 - Criar, concluir, arquivar e excluir só nas próprias (Priority: P1)

**Goal**: Operações de tarefa usam o dono da sessão; pessoa B leva 404 na tarefa de A.

**Independent Test**: User A cria “Pagar conta”; User B não vê e não altera; o registro de A permanece.

### Tests for User Story 3

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T039 [P] [US3] Write failing unit tests that task services take owner id (`allForUser` / `findForUser` / create with `user_id`) in `backend/tests/Unit/ListTasksServiceTest.php`, `backend/tests/Unit/CreateTaskServiceTest.php`, `backend/tests/Unit/ToggleTaskServiceTest.php`, `backend/tests/Unit/ArchiveTaskServiceTest.php`, and `backend/tests/Unit/DeleteTaskServiceTest.php`
- [x] T040 [P] [US3] Write failing feature isolation tests (A vs B, 404 on foreign id) in `backend/tests/Feature/TaskIsolationApiTest.php`

### Implementation for User Story 3

- [x] T041 [US3] Pass owner id through `backend/app/Services/ListTasksService.php`, `backend/app/Services/CreateTaskService.php`, `backend/app/Services/ToggleTaskService.php`, `backend/app/Services/ArchiveTaskService.php`, and `backend/app/Services/DeleteTaskService.php`
- [x] T042 [US3] Pass `request()->user()->id` into task services from `backend/app/Http/Controllers/Api/TaskController.php` (no business rule in the controller)
- [x] T043 [US3] Keep `user_id` out of the JSON in `backend/app/Http/Resources/TaskResource.php`

**Checkpoint**: Isolamento A/B na API

---

## Phase 6: User Story 7 - Tarefas antigas sem dono não são herdadas (Priority: P3)

**Goal**: Órfãs (`user_id` nulo) não aparecem e não são atribuídas no login.

**Independent Test**: Seed de tarefas sem dono; autenticar; lista vazia ou só as novas; no banco as órfãs continuam com `user_id` nulo.

### Tests for User Story 7

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T044 [US7] Write failing feature tests that orphans are omitted from `GET /api/tasks` and remain `user_id` null after Google callback in `backend/tests/Feature/TaskOrphanApiTest.php`

### Implementation for User Story 7

- [x] T045 [US7] Confirm `CompleteGoogleSignInService` still has no `TaskRepositoryInterface` in `backend/app/Services/CompleteGoogleSignInService.php` and extend `backend/tests/Unit/CompleteGoogleSignInServiceTest.php` if needed
- [x] T046 [US7] Confirm `allForUser` ignores null `user_id` in `backend/app/Repositories/EloquentTaskRepository.php`

**Checkpoint**: Órfãs inacessíveis e não herdadas

---

## Phase 7: User Story 2 - Visitante desconectado vê só a tela de entrada (Priority: P1)

**Goal**: Sem sessão a SPA mostra Continuar com Google; sem lista e sem formulário.

**Independent Test**: Render sem sessão → tela de entrada; ausência de “Nova tarefa” / lista.

### Tests for User Story 2

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T047 [P] [US2] Write failing unit tests for `GET /api/session` in `frontend/src/features/auth/authApi.test.ts`
- [x] T048 [P] [US2] Write failing component test: Continuar com Google, no task list, in `frontend/src/features/auth/SignInScreen.test.tsx`
- [x] T049 [US2] Write failing App shell test: signed-out state shows sign-in only in `frontend/src/App.test.tsx`

### Implementation for User Story 2

- [x] T050 [US2] Implement `getSession` with `credentials: 'include'` in `frontend/src/features/auth/authApi.ts` and `frontend/src/features/auth/types.ts`
- [x] T051 [US2] Implement signed-out state in `frontend/src/features/auth/AuthContext.tsx` (no `fetch` in screens)
- [x] T052 [US2] Implement MUI `SignInScreen` (card, primary Continuar com Google → `/auth/google`) in `frontend/src/features/auth/SignInScreen.tsx`
- [x] T053 [US2] Wrap `AuthProvider` in `frontend/src/main.tsx` and gate signed-out UI in `frontend/src/App.tsx` so `TaskProvider` does not mount (no anonymous `GET /api/tasks`)

**Checkpoint**: Visitante só vê a tela de entrada

---

## Phase 8: User Story 1 - Entrar com Google e ver só as próprias tarefas (Priority: P1) 🎯 MVP de produto

**Goal**: Com sessão, dashboard da lista; só tarefas do dono; nome ou e-mail visíveis.

**Independent Test**: `getSession` mockado com usuário → dashboard; `listTasks` mockado → só itens daquele usuário; identidade na casca.

### Tests for User Story 1

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T054 [P] [US1] Write failing tests that `taskApi` sends `credentials: 'include'` in `frontend/src/features/tasks/taskApi.test.ts`
- [x] T055 [P] [US1] Write failing AuthContext test: signed-in user after `getSession` in `frontend/src/features/auth/AuthContext.test.tsx`
- [x] T056 [US1] Write failing App test: signed-in session shows dashboard title and list region in `frontend/src/App.test.tsx`

### Implementation for User Story 1

- [x] T057 [US1] Add `credentials: 'include'` to all calls in `frontend/src/features/tasks/taskApi.ts`
- [x] T058 [US1] Load session on mount in `frontend/src/features/auth/AuthContext.tsx` and mount `TaskProvider` only when signed in in `frontend/src/App.tsx`
- [x] T059 [US1] Show session name or email on the authenticated shell in `frontend/src/layout/DashboardLayout.tsx`

**Checkpoint**: Caminho feliz da spec (lista pessoal na UI)

---

## Phase 9: User Story 5 - Encerrar a sessão e voltar à tela de entrada (Priority: P2)

**Goal**: Sair chama `DELETE /api/session` e volta à tela de entrada.

**Independent Test**: Autenticado → Sair → SignInScreen; lista some.

### Tests for User Story 5

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T060 [P] [US5] Write failing `authApi` test for `DELETE /api/session` in `frontend/src/features/auth/authApi.test.ts`
- [x] T061 [P] [US5] Write failing AuthContext `signOut` test in `frontend/src/features/auth/AuthContext.test.tsx`
- [x] T062 [US5] Write failing test that Sair returns to sign-in in `frontend/src/layout/DashboardLayout.test.tsx`

### Implementation for User Story 5

- [x] T063 [US5] Implement `endSession` in `frontend/src/features/auth/authApi.ts` and `signOut` in `frontend/src/features/auth/AuthContext.tsx`
- [x] T064 [US5] Add Sair on the authenticated shell in `frontend/src/layout/DashboardLayout.tsx` wired through AuthContext (screen does not `fetch`)

**Checkpoint**: Logout devolve a tela de entrada

---

## Phase 10: User Story 6 - Cancelar no Google permanece desconectado (Priority: P3)

**Goal**: `signin=cancelled` / `signin=error` mostram mensagem clara; lista fechada; botão permanece.

**Independent Test**: Render com query de cancelamento → alerta + Continuar com Google; sem lista.

### Tests for User Story 6

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T065 [US6] Write failing tests for cancelled and error messages with button still present in `frontend/src/features/auth/SignInScreen.test.tsx` and `frontend/src/features/auth/AuthContext.test.tsx`

### Implementation for User Story 6

- [x] T066 [US6] Read `signin` query, set product copy from `specs/001-google-sign-in/contracts/auth.md`, and keep SignInScreen mounted in `frontend/src/features/auth/AuthContext.tsx` and `frontend/src/App.tsx`

**Checkpoint**: Cancelar/erro no Google não abre a lista

---

## Phase 11: Polish & Cross-Cutting Concerns

**Purpose**: Segredos, regressão e guia de validação

- [x] T067 [P] Assert JSON error bodies never contain client secret text in `backend/tests/Feature/GoogleAuthTest.php`
- [x] T068 [P] Confirm `.env` stays ignored in `.gitignore` and `backend/.gitignore`; no Google secret in `frontend/`
- [x] T069 Run `docker compose exec backend php artisan test` covering `backend/tests/` and `docker compose exec frontend npm test` covering `frontend/src/**/*.test.ts*`
- [x] T070 Walk `specs/001-google-sign-in/quickstart.md` scenarios that do not require a real Google account (tests + 401 curl without cookie)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: Sem dependências
- **Foundational (Phase 2)**: Depende da Fase 1. Inclui `users` e sessão. **Termina em T030**
- **T030**: Checkpoint humano. Bloqueia Fase 3
- **Fase 3 (dono)**: Depende de T030. Bloqueia US4, US3, US7
- **US4 → US3 → US7**: Isolamento da API, nesta ordem (401 antes de dono nas operações; órfãs depois do filtro por `user_id`)
- **US2 → US1 → US5 → US6**: UI só depois do isolamento da API
- **Polish**: Depois das stories desejadas

### User Story Dependencies

- **US4**: Depois da Fase 3
- **US3**: Depois de US4 (rotas já exigem sessão)
- **US7**: Depois de US3 (`allForUser` existe)
- **US2**: Depois da Fase 2 (precisa de `GET /api/session`) e depois do isolamento API para não reabrir lista anônima
- **US1**: Depois de US2 e US3
- **US5**: Depois de US1 (precisa do dashboard)
- **US6**: Depois de US2 (precisa da SignInScreen)

### Within Each User Story

- Testes FAILING antes da implementação
- Modelo/repositório antes de Service
- Service antes de controller/rota
- Gateway (`*Api.ts`) antes de Context antes da tela

### Parallel Opportunities

- T002, T003, T004 depois de T001
- T007 e T008 depois de T006
- T011, T012, T015 depois de T010
- T039 e T040 depois da Fase 3
- T047 e T048 na US2
- T054 e T055 na US1
- T060 e T061 na US5
- T067 e T068 no polish

**Não paralelizar** stories de UI com a Fase 2 ainda aberta, nem a migration T031 com T030 desmarcado.

---

## Parallel Example: Phase 2 (identidade)

```bash
# Depois de T006:
Task: "Implement FakeIdentityProvider in backend/app/Identity/FakeIdentityProvider.php"
Task: "Implement Socialite adapter in backend/app/Identity/SocialiteIdentityProvider.php"
```

## Parallel Example: User Story 3

```bash
Task: "Write failing unit tests for owner id in backend/tests/Unit/*TaskServiceTest.php"
Task: "Write failing feature isolation tests in backend/tests/Feature/TaskIsolationApiTest.php"
```

## Parallel Example: User Story 2

```bash
Task: "Write failing authApi tests in frontend/src/features/auth/authApi.test.ts"
Task: "Write failing SignInScreen tests in frontend/src/features/auth/SignInScreen.test.tsx"
```

---

## Implementation Strategy

### Até o checkpoint (obrigatório)

1. Fase 1 Setup
2. Fase 2 identidade/sessão/`users`
3. **PARAR em T030** — humano aprova, edita ou volta à spec
4. Não gerar `*_add_user_id_to_tasks_table.php` antes disso

### MVP API (depois do checkpoint)

1. Fase 3 dono nullable
2. US4 401
3. US3 isolamento
4. Validar com `docker compose exec backend php artisan test`

### MVP de produto (UI)

1. US2 tela de entrada
2. US1 lista pessoal 🎯
3. Depois US5 e US6

### Incremental Delivery

1. Setup + identidade → demo: callback fake + `GET /api/session`
2. Checkpoint humano
3. API isolada → demo: A vs B no PHPUnit
4. US2+US1 → demo na tela
5. US5+US6 → logout e cancelamento

### Parallel Team Strategy

Este laboratório é sequencial no checkpoint. Depois de T030+Fase 3, uma pessoa pode fechar US4/US3/US7 no backend enquanto outra só começa UI **depois** de US4 (para o App não voltar a listar anônimo).

---

## Notes

- [P] = arquivos diferentes, sem depender de tarefa incompleta
- Labels [US1]–[US7] batem com as stories de `spec.md`, não com o número da fase
- T030 só o humano marca
- Teste primeiro; confirmar falha; só então implementar
- Sem E2E; sem falar com a internet nos testes (FakeIdentityProvider)
- Não implementar código neste comando — só este `tasks.md`
