# Conformidade com AGENTS.md — login Google e GitHub

Auditoria das duas implementações da prática SDD contra o `AGENTS.md` do laboratório.

- **Google:** Spec Kit (`specs/001-google-sign-in/`)
- **GitHub:** OpenSpec (`openspec/changes/login-com-github/`)

Testes no momento desta auditoria (Docker): backend **49 passed**; frontend **23 passed**.

## Resumo

| Convenção do AGENTS.md | Google (Spec Kit) | GitHub (OpenSpec) |
|---|---|---|
| TDD / tipos de teste separados | Sim | Sim |
| Regra no Service, não no controller/tela | Sim | Sim |
| Persistência via Repository | Sim | Sim |
| SOLID (caso de uso novo = peça nova) | Sim | Sim |
| POSA (não pular camada) | Sim | Sim |
| GoF só quando necessário | Sim (Command, Adapter, Factory) | Sim (reusou Adapter; Service novo) |
| Frontend: Context + MUI; tela sem `fetch` | Sim | Sim |
| Docker; sem PHP/Node no host | Sim | Sim |
| Não versionar segredos | Sim | Sim |
| Sem E2E | Sim | Sim |

**Veredito:** as duas features seguiram o `AGENTS.md`. Onde há ressalva, é residual (método `all()` legado no repositório de tarefas), não uma violação do fluxo atual.

## Como cada ferramenta exercitou o AGENTS.md

Os dois **passaram** no checklist. A diferença útil para comparar Spec Kit e OpenSpec não é “quem obedeceu”, e sim **em que momento o AGENTS.md foi pressionado**.

| Dimensão | Spec Kit (Google) | OpenSpec (GitHub) |
|---|---|---|
| Papel do AGENTS.md | Virrou regra de projeto **junto** com a constituição e o plan: TDD, Service, Repository e Docker entraram nas ~70 tasks antes do domínio existir | Funcionou como **restrição de brownfield**: não quebrar Google, não unir por e-mail, não inchar Service existente, não pular camada |
| O que o AGENTS.md forçou criar | Peças novas do zero (`IdentityProviderInterface`, Services de sessão, `features/auth`, dono em `tasks`) no padrão POSA | Uma peça nova (`CompleteGitHubSignInService`) e reuso do que já estava certo (sessão, `TaskProvider`, fake, rotas de tarefa) |
| Risco se o AGENTS.md fosse ignorado | Regra no controller OAuth, lista ainda global, teste falando com a internet | Unir Google e GitHub pelo e-mail, ou meter GitHub dentro do `CompleteGoogleSignInService` |
| Evidência concreta | Checkpoint T030 + isolamento A/B + fake nos testes | Teste unitário “não reutiliza usuário Google com o mesmo e-mail” + `GoogleAuthTest` verde após o apply |

Em uma linha para a tabela comparativa das ferramentas: **Spec Kit usou o AGENTS.md para instaurar o padrão; OpenSpec usou o AGENTS.md para não destruir o padrão ao acrescentar o segundo provedor.**

## Backend

### Regra de negócio no Service

- Google: `CompleteGoogleSignInService` faz find-or-create por `google_id` / `providerUserId`.
- GitHub: `CompleteGitHubSignInService` faz find-or-create **só** por `github_id` (não une por e-mail).
- Tarefas: `List` / `Create` / `Toggle` / `Archive` / `Delete` recebem o dono da sessão; isolamento fica no Service + Repository.
- Controllers de auth: só HTTP (redirect, catch de cancelamento/erro, `Auth::login`, regenerar sessão). Não há Eloquent nem regra de “quem é o usuário” no controller.
- `TaskController`: só passa `$request->user()->id` ao Service.

Isso atende: *“Regra de negócio na camada de aplicação (Service), não no controller”* e o **Não fazer**.

### Persistência atrás de abstração

- `UserRepositoryInterface` (`findByGoogleId`, `findByGithubId`, `create`)
- `TaskRepositoryInterface` (`allForUser`, `findForUser`, …)
- Implementações Eloquent só no Adapter; Services dependem da interface (DIP).

### SOLID e OCP

- Caso de uso novo = classe nova: `CompleteGoogleSignInService` e depois `CompleteGitHubSignInService` (OpenSpec **não** inchou o Service do Google).
- `IdentityProviderInterface` + `SocialiteIdentityProvider` (driver) + `FakeIdentityProvider` (LSP/DIP nos testes).
- Um motivo para mudar por peça: HTTP / caso de uso / persistência / adaptador OAuth.

### POSA

Apresentação (Controller) → aplicação (Service) → persistência (Repository). Controllers de auth e de tarefa **não** chamam `User::` / `Task::`.

### GoF (quando necessário)

- **Command:** um Service por caso de uso (`handle`).
- **Adapter:** Eloquent e Socialite atrás de interfaces.
- **Factory:** container (binding contextual Google vs GitHub).
- Sem Observer/Strategy inventados.

### Tipos de teste

| Tipo | Exemplos Google | Exemplos GitHub |
|---|---|---|
| Unidade | `CompleteGoogleSignInServiceTest` | `CompleteGitHubSignInServiceTest` (inclui “não reutiliza Google com mesmo e-mail”) |
| Feature | `GoogleAuthTest`, `SessionApiTest`, isolamento/órfãs/401 | `GitHubAuthTest`, `GitHubTaskIsolationApiTest`, `GitHubUserPersistenceTest` |

Fake do provedor: sem internet nos testes. Sem E2E.

## Frontend

- Domínio `features/auth` separado de `features/tasks`.
- `SignInScreen` / `DashboardLayout` leem `AuthContext`; **não** fazem `fetch`.
- `fetch` só em `authApi` e `taskApi` (camada de acesso à API).
- `TaskProvider` só monta com sessão (App gate) — evita `GET /api/tasks` anônimo.
- UI MUI (card, hierarquia, dois botões Google/GitHub).
- Testes: unidade (`authApi`, `taskApi`), integração (`AuthContext`, `TaskContext`), componente (`SignInScreen`, `App`, layout).

## Não fazer (checklist)

| Item | Situação |
|---|---|
| Não colocar regra no controller/tela | Ok |
| Não pular camada | Ok |
| Não GoF por aplicar | Ok |
| Não pular/misturar tipos de teste | Ok |
| Não layout cru | Ok (tela de entrada MUI) |
| Não instalar PHP/Node no host | Ok (Docker) |
| Não misturar backend/frontend na mesma pasta | Ok |
| Não versionar segredos | Ok (`.env` ignorado; só nomes de variável e dummies de teste) |

## Ressalva documentada (já citada na prática)

`EloquentTaskRepository::all()` ainda existe e devolve todas as tarefas. O fluxo autenticado usa `allForUser` / `findForUser`. Não é falha das features novas, mas é um atalho legado: se alguém voltar a chamar `all()`, a lista global reabre. Vale manter no relatório como achado da revisão de diff.

## Conclusão para o relatório

Spec Kit e OpenSpec, nestas duas features, **respeitaram o AGENTS.md** do laboratório da aula anterior: TDD, Service, Repository, POSA, SOLID (peça nova por caso de uso) e frontend em camadas. A evolução da ferramenta (prompt/ambiente → SDD) ficou no mesmo projeto de propósito; as convenções permanentes do agente continuaram valendo.
