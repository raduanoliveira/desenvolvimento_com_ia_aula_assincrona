# Etapa 4. Revisão arquitetural

Resumo gerado com apoio do agente, a partir do código do `ia-dev-lab` na branch `feature/task-priority`.

## Módulos

Dois processos no Docker, um contrato HTTP entre eles.

- **backend (Laravel, porta 8000).** Domínios `tasks` e `identity`. Apresentação: `TaskController`, `TaskReminderController`, `SessionController`, `GoogleAuthController`, `GitHubAuthController`, Form Requests e Resources. Aplicação: um Service por caso de uso (`CreateTaskService`, `ListTasksService`, `UpdateTaskTitleService`, `UpdateTaskPriorityService`, `ToggleTaskService`, `ArchiveTaskService`, `DeleteTaskService`, `ListDueTomorrowRemindersService`, `CompleteGoogleSignInService`, `CompleteGitHubSignInService`, `EndSessionService`). Persistência: `TaskRepositoryInterface` / `EloquentTaskRepository` e o par equivalente de usuário. Identidade: `IdentityProviderInterface`, `SocialiteIdentityProvider` e `FakeIdentityProvider`. A tarefa na aplicação é `App\Domain\Task`; o Model Eloquent fica no Adapter.
- **frontend (React, porta 5173).** `features/auth` (tela, Context, `authApi`) e `features/tasks` (form, lista, filtro, toaster, Context, `taskApi`). A casca (`App`, `DashboardLayout`) não chama a API.

O contrato visível é REST + cookie de sessão: rotas em `backend/routes/api.php` e funções em `taskApi.ts` / `authApi.ts`. Não há OpenAPI.

## Dependências e camadas

No backend, o fluxo observado é apresentação → aplicação → persistência. O `TaskController` só valida, chama o Service e devolve Resource. Nenhum Service instancia Eloquent com `Task::query()`; isso fica no repositório, que traduz Model ↔ `App\Domain\Task`. O `AppServiceProvider` faz o bind das interfaces.

No frontend, o fluxo é tela → Context → `taskApi`. `TaskList` e `TaskForm` usam o Context; o `fetch` fica no gateway.

A identidade já é um porto: Google e GitHub compartilham `IdentityProviderInterface`. Extrair isso de novo, como processo separado, duplicaria sessão e cookie.

## Pontos de acoplamento

1. **Contrato da tarefa (fechado).** `TaskRepositoryInterface` devolve `App\Domain\Task`. Os Services lançam `TaskNotFoundException`. O Eloquent permanece só em `EloquentTaskRepository`. Evidência: `tests/Unit/EloquentTaskRepositoryTest.php`.
2. **Contrato HTTP implícito.** O JSON de `TaskResource` e o tipo `Task` do frontend se espelham por convenção, sem schema versionado.
3. **Filtro em memória.** `ListTasksService` e `ListDueTomorrowRemindersService` carregam as tarefas do dono e filtram na coleção. Cabe neste volume; não é um segundo serviço.
4. **`all()` sem dono** na interface do repositório: resto da lista anônima, anterior à autenticação. Nenhum caso de uso autenticado deveria usá-lo.

Não há violação de pular camada no sentido do `AGENTS.md`: a tela não chama a API direto; o controller não grava regra; o Service não abre query.

## Decisão

**O projeto está no tamanho certo** quanto a processos: não extraio um serviço de tarefas nem de login. **Fechei o contrato da aplicação** com `App\Domain\Task`, sem mudar schema nem a tela.

### Critérios (não só opinião)

- **Modularidade.** Já existem dois módulos de produto (tarefa e identidade) e dois lados (API e tela), com caso de uso novo virando Service novo. Fatiar em pacotes ou bounded contexts extras não criaria um segundo consumidor. O DTO de Task só fecha o porto que já existia.
- **Baixo acoplamento.** Extrair “serviço de tarefas” ou “serviço de login” viraria acoplamento de rede, cookie e deploy. Fechar o tipo da tarefa reduz acoplamento ao ORM sem esse custo.
- **Contratos claros.** O vazamento Eloquent era o ponto fraco do porto. Com entidade de domínio, o Service testa e evolui sem `ModelNotFoundException`. O contrato HTTP implícito permanece: só a tela deste repositório consome a API.

### O que não faço

- Não extraio identidade nem tarefas para outro processo.
- Não introduzo OpenAPI nesta etapa.
- Não crio migration: o schema de `tasks` permanece.

### Verificação

No Docker: 68 testes no backend e 32 no frontend, todos passando.
