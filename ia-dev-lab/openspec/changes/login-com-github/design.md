## Context

Login com Google já está em produção no laboratório: sessão em cookie, `IdentityProviderInterface` + `IdentityUser`, `CompleteGoogleSignInService`, `GET /auth/google` e callback, `GET`/`DELETE /api/session`, tarefas com `user_id` e rotas de tarefa atrás de `auth`. A tela de entrada só tem Continuar com Google. Ver proposal.md (Why) e `specs/identity/github-sign-in/spec.md` para o comportamento exigido.

O provedor concreto `SocialiteIdentityProvider` está acoplado ao driver `google`. `IdentityUser` expõe `googleId`. `users.google_id` é UNIQUE NOT NULL. O container liga um único `IdentityProviderInterface` → Socialite (app) ou `FakeIdentityProvider` (testes). GitHub entra como segundo provedor no **mesmo** sistema de usuários e sessões — não um segundo módulo de auth paralelo.

## Goals / Non-Goals

**Goals:**

- Acrescentar o fluxo GitHub reusando sessão, dono de tarefa e tela de entrada.
- Manter a abstração de identidade: Socialite só no adaptador; testes usam fake, sem internet.
- Um caso de uso novo (`CompleteGitHubSignInService`), sem inflar o service do Google.
- Persistência de `github_id` atrás do repositório de usuários; `google_id` deixa de ser obrigatório para caber usuário só-GitHub.
- Contextualizar o bind do provedor (Google vs GitHub) para o controller correspondente.

**Non-Goals:**

- Unir contas, login por e-mail/senha, 2FA, papéis de admin.
- Segundo `user_id` ou segundo sistema de tarefas.
- Checkpoint humano do tipo T030 (tarefas órfãs): esta migration é só em `users`.
- Trocar o contrato de query `signin=cancelled|error` por um contrato GitHub-específico.

## Decisions

### 1. Mesmo DTO e mesma interface; `providerUserId` no lugar de `googleId`

`IdentityProviderInterface` permanece (`redirectUrl`, `userFromCallback`). `IdentityUser` passa a expor `providerUserId`, `name` e `email`. Cada Service de completar entrada mapeia esse id para a coluna do provedor (`google_id` ou `github_id`).

**Por quê:** a interface já existe; o nome `googleId` mentiria para o GitHub. Um DTO por provedor duplicaria fakes e controllers.

**Alternativa considerada:** campo `githubId` opcional no mesmo DTO — mistura dois provedores num objeto e força nulls. Descartada.

### 2. Um adaptador Socialite parametrizado pelo driver, não um segundo “sistema”

`SocialiteIdentityProvider` recebe o nome do driver (`google` | `github`) no construtor. Redirect e `user()` usam esse driver. Cancelamento (`access_denied` / `consent_required`) e falha genérica continuam lançando as mesmas exceções já tratadas pelo controller.

**Por quê:** Socialite já tem driver GitHub; Template Method não se justifica — só muda o nome do driver. Factory no container (contextual binding) monta as duas instâncias.

**Alternativa considerada:** classe `SocialiteGitHubIdentityProvider` irmã — duplicaria o tratamento de erro. Descartada enquanto o fluxo for idêntico.

### 3. Binding contextual por controller; fake nos testes

No app:

- `GoogleAuthController` → Socialite driver `google`
- `GitHubAuthController` → Socialite driver `github`

Nos testes, `TestCase` liga os dois controllers a fakes (sem HTTP). Feature tests do GitHub rebindem um fake com `providerUserId` de GitHub, modos success/cancelled/error — o mesmo padrão de `GoogleAuthTest`.

**Por quê:** um bind global único não distingue os drivers. Contextual binding respeita DIP sem o controller instanciar Socialite.

### 4. Caso de uso novo: `CompleteGitHubSignInService`

Find-or-create **somente** por `github_id`. Nunca busca por e-mail, nunca preenche `google_id` no mesmo registro. `CompleteGoogleSignInService` permanece só Google.

`UserRepositoryInterface` ganha `findByGithubId`; `findByGoogleId` permanece.

**Por quê:** Command — um caso de uso por Service (OCP). Unir por e-mail violaria a spec.

### 5. Modelo `users`: `google_id` nullable + `github_id` nullable unique

Migration nova (não reescrever a create original):

- `google_id`: remover NOT NULL; UNIQUE permanece (vários NULL ok no SQLite/MySQL do laboratório).
- `github_id`: string nullable unique.

Invariante de aplicação: cada linha tem exatamente um dos dois ids preenchido. Usuários Google existentes não mudam. Não há backfill nem união.

E-mail GitHub pode vir vazio (conta com e-mail privado). O Service preenche `email` com string vazia ou com o login público se o provedor não devolver e-mail; `name` usa name, nickname ou login. Não falha só por e-mail ausente — a spec aceita nome **ou** e-mail na sessão.

### 6. Rotas e contrato HTTP espelham o Google

- `GET /auth/github` → 302 para o GitHub (URL do adaptador)
- `GET /auth/github/callback` → sucesso: sessão + 302 `{FRONTEND_URL}/`; cancelado: `/?signin=cancelled`; erro: `/?signin=error`
- `GET`/`DELETE /api/session` inalterados (já servem qualquer `User`)

Mensagens na SPA ficam neutras o suficiente para os dois provedores (entrada não concluída / não foi possível entrar), sem citar só o Google — senão o cancelamento GitHub mentiria.

**Por quê:** o frontend já interpreta essas queries; um terceiro par (`github-cancelled`) inflaria a tela sem ganho de produto.

**Alternativa considerada:** query `provider=github` — extra, não pedida. Descartada.

### 7. Frontend: segunda ação na mesma tela, mesmo Context

`authApi` exporta `githubStartUrl` (`/auth/github`). `AuthContext` expõe a URL; `SignInScreen` ganha o botão Continuar com GitHub ao lado do Google. `TaskProvider` continua montado só com sessão. Sem chamada de API na tela além do que já existe.

### 8. Segredos e config

`.env` / `.env.example` (valores vazios ou URI de callback não secreta): `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, `GITHUB_REDIRECT_URI` (`http://localhost:8000/auth/github/callback`). `config/services.php` lê o driver `github` do env. `phpunit.xml` recebe dummies, como o Google. Nada disso no repositório com valor real.

Escopo OAuth GitHub: o padrão do Socialite (`user:email`) basta; não pedimos scopes extras.

## Risks / Trade-offs

- **[Risco] `google_id` NOT NULL quebra usuários só-GitHub** → Mitigation: migration torna `google_id` nullable antes de criar contas GitHub; testes de feature cobrem os dois formatos de linha.
- **[Risco] Alguém unir por e-mail “para ajudar”** → Mitigation: spec + teste de feature com o mesmo e-mail nos dois provedores; o Service GitHub não chama `findByGoogleId` nem busca por e-mail.
- **[Risco] Bind global do fake faz callback GitHub gravar `google_id`** → Mitigation: fakes contextualizados e Service GitHub que só escreve `github_id`; teste unitário do Service e feature do callback.
- **[Risco] E-mail privado no GitHub deixa `email` vazio** → Mitigation: fallback de nome/login; sessão ainda identifica a pessoa; não é união de contas.
- **[Trade-off] Mensagens `signin=*` deixam de citar só o Google** → Aceito: um contrato só, copy honesta para os dois botões.
- **[Trade-off] Sem CHECK no banco “um dos dois ids”** → Aceito neste laboratório; a invariante vive nos Services. Um CHECK extra seria overkill.

## Migration Plan

1. Subir o ambiente com `docker compose` (sem instalar PHP/Node/banco no host).
2. Aplicar a migration de `users` (`google_id` nullable, `github_id` nullable unique). Rollback: drop `github_id` e voltar NOT NULL em `google_id` **somente** se não existirem linhas só-GitHub; se existirem, rollback exige apagar ou migrar essas linhas — neste laboratório, recrear o volume SQLite é aceitável.
3. Preencher `GITHUB_*` no `.env` local (nunca commitado). Registrar o callback no aplicativo GitHub OAuth.
4. Sem mudança em `tasks`; sem backfill de dono.
5. Google continua o caminho existente; quem não clicar em GitHub não é afetado.

## Open Questions

Nenhuma que altere spec, abordagem ou fatia de tarefas. Assunção registrada: reutilizar `signin=cancelled|error` com copy neutra.
