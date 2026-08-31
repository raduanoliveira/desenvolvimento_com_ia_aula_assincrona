# Implementation Plan: Entrar com Google

**Branch**: `001-google-sign-in` | **Date**: 2026-08-31 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/001-google-sign-in/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command; its definition describes the execution workflow.

## Summary

Quem usa o ToDo entra com a conta Google, vê só as próprias tarefas e não herda a lista anônima antiga. Pedido sem sessão é recusado; cancelar no Google deixa a pessoa desconectada com mensagem clara; sair volta à tela de entrada.

Abordagem no stack **já existente**: API Laravel no Docker (SQLite, PHPUnit) + React/TypeScript/Context/MUI. OAuth Google via Laravel Socialite **atrás** de `IdentityProviderInterface` (fake nos testes). Sessão Laravel em cookie. Dono em `tasks.user_id` nullable, sem backfill. Casos de uso novos viram Services novos; Services de tarefa passam a receber o dono. Frontend: `features/auth` (gateway + Context + tela de entrada); a tela não faz `fetch`. Segredos só em `.env`.

**Checkpoint humano obrigatório:** parar **antes** da migration que adiciona `user_id` em `tasks`. Ver [data-model.md](./data-model.md) e [quickstart.md](./quickstart.md).

## Technical Context

**Language/Version**: PHP 8.2 (Laravel 11) no container `backend`; TypeScript 5.7 / React 19 no container `frontend`

**Primary Dependencies**: Laravel Framework 11, Laravel Socialite (somente Adapter do Google), PHPUnit 11, Mockery; React 19, MUI 6, Context API, Vitest, Testing Library. Sem Sanctum, sem JWT, sem instalar PHP/Node no host.

**Storage**: SQLite (`DB_DATABASE` no Docker; `:memory:` no PHPUnit), já usado pelo lab

**Testing**: `docker compose exec backend php artisan test` (Unit = regra sem HTTP/banco de verdade via mock; Feature = rota, status, JSON, persistência). `docker compose exec frontend npm test` (gateway, Context, componente). Sem E2E.

**Target Platform**: App web local via Docker Compose (`localhost:5173` + `localhost:8000`)

**Project Type**: Monorepo web (API REST + SPA), pastas `backend/` e `frontend/` já existentes

**Performance Goals**: Entrada com Google + lista visível em menos de 2 minutos (SC-001). Depois de autenticado, listar/criar no lab local com percepção imediata (&lt; 2 s)

**Constraints**: TDD; regra só em Service; persistência só via Repository; provedor Google abstrato; `.env` fora do git; POSA sem pular camada; layout MUI moderno; **stop** antes da migration de dono em `tasks`; órfãs inacessíveis e não herdadas

**Scale/Scope**: Laboratório (poucas contas Google, uma lista por pessoa). Uma tela de entrada + dashboard de tarefas já existente. GitHub login fora de escopo.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Princípio | Gate | Status |
|-----------|------|--------|
| I. Spec antes do código | Plano deriva de `spec.md`; este comando não gera código de produto | Pass |
| II. Teste primeiro | Tipos de teste iguais ao lab; Given/When/Then da spec viram testes; sem E2E | Pass |
| III. Camadas e um Service por caso de uso | Controller HTTP; Services novos de sessão/Google; Services de tarefa só ganham o dono; Repository; frontend tela → Context → `*Api` | Pass |
| IV. Docker é o ambiente | Comandos só via `docker compose` no quickstart | Pass |
| V. Identidade e dados | Tarefas do autenticado; secret em env; `IdentityProviderInterface` | Pass |
| Checkpoint `user_id` | Execução para **antes** da migration de dono em `tasks`; humano aprova/edita/volta à spec | Pass |
| Layout MUI | Tela de entrada em card/hierarquia; dashboard atual permanece | Pass |
| Sem GoF por aplicar | Adapter no provedor e no repositório (problema real); Command = um Service por caso de uso; Factory = container | Pass |

Nenhuma violação. Seção Complexity Tracking vazia.

**Re-check pós-Phase 1:** contratos HTTP colocam 401 na borda de apresentação e dono no Service/Repositório; fake do provedor nos testes; `user_id` omitido do JSON da tarefa; checkpoint ainda na frente da migration de `tasks`. Gates continuam Pass.

## Project Structure

### Documentation (this feature)

```text
specs/001-google-sign-in/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   ├── auth.md
│   ├── tasks.md
│   └── ui.md
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created by /speckit-plan)
```

### Source Code (repository root)

Estrutura **atual** do lab, com os arquivos que esta feature acrescenta ou altera (implementação só depois de `/speckit-tasks`):

```text
backend/
├── app/
│   ├── Http/Controllers/Api/   # TaskController (passa user da sessão); SessionController novo
│   ├── Http/Controllers/Auth/  # redirect + callback Google (HTTP only)
│   ├── Http/Requests/          # StoreTaskRequest existente
│   ├── Http/Resources/         # TaskResource; UserResource (sessão)
│   ├── Models/                 # Task; User novo
│   ├── Repositories/
│   │   ├── Contracts/          # TaskRepositoryInterface (por dono); UserRepositoryInterface
│   │   ├── EloquentTaskRepository.php
│   │   └── EloquentUserRepository.php
│   ├── Services/               # Create/List/Toggle/Archive/Delete + CompleteGoogleSignIn + EndSession
│   └── Identity/               # IdentityProviderInterface; Socialite adapter; Fake para testes
├── config/                     # services.php (Google lê env); cors.php (credentials)
├── database/migrations/        # create_users_table; add_user_id_to_tasks (DEPOIS do checkpoint)
├── routes/web.php              # GET /auth/google e callback
├── routes/api.php              # /api/session + tasks autenticadas
└── tests/
    ├── Unit/                   # Services + isolamento de dono (repositório mockado)
    └── Feature/                # 401, A/B, órfãs, callback fake, logout

frontend/
├── src/
│   ├── features/auth/          # authApi.ts, AuthContext.tsx, SignInScreen.tsx + testes
│   ├── features/tasks/         # taskApi com credentials; TaskContext só com sessão
│   ├── layout/DashboardLayout.tsx  # nome/e-mail + Sair
│   ├── App.tsx                 # gate entrada vs dashboard
│   └── main.tsx                # AuthProvider envolvendo a árvore
└── src/**/*.test.ts(x)
```

**Structure Decision**: Manter o monorepo Laravel + React já adotado (ADR 0002). Identidade é domínio novo em `features/auth` e `app/Identity` + `app/Services` de sessão; não misturar com `features/tasks` nem colocar OAuth no `TaskController`.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

Nenhuma violação a justificar.

## Phase 0 & Phase 1

- [research.md](./research.md) — decisões (Socialite+Adapter, sessão cookie, órfãs, checkpoint, 401/404, Services, AuthContext, env, tipos de teste)
- [data-model.md](./data-model.md) — User, Session, Task.user_id nullable
- [contracts/](./contracts/) — auth, tasks, ui
- [quickstart.md](./quickstart.md) — Docker, env, **stop** na migration de dono, comandos de teste

Próximo comando: `/speckit-tasks` (este plano não cria `tasks.md` nem código).
