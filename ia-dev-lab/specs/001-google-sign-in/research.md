# Research: Entrar com Google

Todas as dúvidas do contexto técnico foram resolvidas com o stack já existente do ia-dev-lab e com as restrições da constituição. Nada de stack novo.

## 1. Provedor Google atrás de abstração

**Decision**: Introduzir `IdentityProviderInterface` na camada de aplicação. A implementação real usa Laravel Socialite só como Adapter. Nos testes, o container recebe um fake que devolve identidade fixa (ou erro/cancelamento), sem HTTP e sem internet.

**Rationale**: A constituição exige que o provedor fique atrás de uma abstração para a regra e os testes não falarem com a internet. Socialite é o adapter oficial do Laravel para Google; não substitui o stack, só preenche o OAuth. Os Services de entrar/completar sessão dependem da interface, não do Socialite.

**Alternatives considered**:

- Chamar a API do Google direto nos Services — mistura internet com regra e impede teste de unidade.
- Google Identity Services no browser (token no frontend) — colocaria o fluxo de identidade na tela e exigiria Client ID no frontend; o secret ainda no backend, mas a regra de “continuar com Google” sairia da camada de aplicação.
- Laravel Socialite no controller, sem interface — testes de feature falariam com a internet ou precisariam mockar a fachada global; fere DIP e a constituição.

## 2. Sessão, não token solto

**Decision**: Sessão Laravel em cookie. O frontend envia `credentials: 'include'`. CORS passa a aceitar credenciais a partir de `FRONTEND_URL` (`http://localhost:5173`). Rotas de login/callback usam middleware web (redirect). Rotas `/api/session` e `/api/tasks` usam sessão autenticada. Sem JWT e sem Sanctum neste laboratório.

**Rationale**: A spec fala em sessão. Frontend e API já correm em `localhost` em portas diferentes; cookies de `localhost` são same-site entre portas no Chrome atual, então o cookie de sessão do backend é enviado no `fetch` com credenciais. Menos peças do que Sanctum, e o aluno já conhece sessão Laravel.

**Alternatives considered**:

- Laravel Sanctum SPA — correto para produção, mas adiciona CSRF cookie e `statefulApi` sem ganho pedagógico neste lab.
- JWT no `Authorization` — a spec pede sessão; o token viraria estado no frontend e desviaria do vocabulário do produto.
- Proxy Vite same-origin — funcionaria, mas mudaria `VITE_API_URL` sem necessidade se CORS + credenciais estiverem certos.

## 3. Dono da tarefa e tarefas órfãs

**Decision**: Tabela `users` (identidade Google). Em `tasks`, coluna `user_id` **nullable**, FK para `users`, **sem backfill**. Listar/alterar filtra por `user_id` da sessão. Tarefas com `user_id` nulo não aparecem para ninguém autenticado e não são atribuídas. Não há migration `NOT NULL` nesta feature.

**Rationale**: FR-010 e FR-011: órfãs não são herdadas. Nullable + filtro por dono cumpre isso sem apagar dados. Exigir `NOT NULL` forçaria decidir o destino das órfãs (apagar, atribuir, ou falhar a migration).

**Alternatives considered**:

- `user_id` obrigatório na mesma migration — quebraria linhas existentes ou exigiria herança/apagar; a spec proíbe herança e o checkpoint humano existe justamente para isso.
- Soft-delete em massa das órfãs — fora do pedido; a spec manda permanecerem armazenadas e inacessíveis.

## 4. Checkpoint humano

**Decision**: A implementação **para antes** da migration que adiciona `user_id` em `tasks`. Criar `users` e o fluxo de sessão pode ocorrer antes. Só depois da revisão humana (aprovar, editar ou voltar à spec) corre a migration de dono e o isolamento nas tarefas.

**Rationale**: Constituição e pedido do plano: “parar ANTES da migração que passa a exigir dono nas tarefas”. `user_id` em `tasks` é essa mudança de dono. A revisão confirma: sem backfill, coluna nullable, órfãs invisíveis.

**Alternatives considered**:

- Checkpoint só antes de `NOT NULL` — frágil: o aluno já teria `user_id` sem revisão do impacto nas órfãs.
- Checkpoint depois de migrate — tarde demais; o dado já mudou.

## 5. Recusa sem sessão e isolamento entre pessoas

**Decision**: Middleware de autenticação nas rotas de tarefa e de sessão: sem sessão → **401** e corpo sem lista. Tarefa de outro dono (ou órfã) → o Service/Repositório não encontra para aquele usuário → **404** (não 403), para não confirmar que o id existe. O controller não contém essa regra: recebe o usuário autenticado e passa o id do dono ao Service.

**Rationale**: FR-006 pede recusa sem devolver tarefa de ninguém (401 sem payload de lista). FR-005 pede recusa de alteração alheia; 404 evita vazamento de existência e reutiliza `ModelNotFoundException` já usada nos Services atuais.

**Alternatives considered**:

- 403 em tarefa alheia — recusa explícita, mas revela que o id existe.
- Filtrar só no controller — regra de dono na apresentação; fere POSA.

## 6. Services e repositórios (não inchar o que existe)

**Decision**: Casos de uso **novos** viram Services novos: completar entrada Google, encerrar sessão, obter sessão atual. Os Services de tarefa **já existentes** passam a receber o id do dono; não ganham lógica de OAuth. `TaskRepositoryInterface` ganha consulta por dono (`allForUser`, `findForUser`). Novo `UserRepositoryInterface` para achar/criar pessoa por `google_id`.

**Rationale**: Constituição III: caso de uso novo = peça nova; persistência atrás de abstração; controller sem Eloquent e sem regra.

**Alternatives considered**:

- Um `AuthService` genérico com login, logout e Google — infla uma classe com vários motivos para mudar.
- `User::query()` dentro do Service de tarefa — pula o repositório.

## 7. Frontend: Context de identidade separado da lista

**Decision**: Novo domínio `frontend/src/features/auth/`: `authApi` (gateway), `AuthContext` (estado), `SignInScreen` (MUI). `App` escolhe tela de entrada **ou** dashboard conforme a sessão. `TaskProvider` só monta (e só chama a API de tarefas) quando há sessão. Telas não fazem `fetch`. `taskApi` passa a enviar cookies (`credentials: 'include'`).

**Rationale**: POSA no frontend: apresentação → estado → API. A lista não deve disparar GET `/tasks` anônimo (FR-001, FR-006). Auth e tarefas são motivos de mudança diferentes (SOLID-S).

**Alternatives considered**:

- Colocar `user` dentro de `TaskContext` — mistura identidade com lista.
- `fetch` na `SignInScreen` — fura a camada.

## 8. Segredos

**Decision**: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` e `FRONTEND_URL` só em variáveis de ambiente. `.env` continua no `.gitignore`. `.env.example` traz chaves **vazias**. Mensagens de erro são texto fixo de produto, nunca o corpo cru do provedor. O frontend **não** lê o secret.

**Rationale**: FR-012 e constituição V.

**Alternatives considered**:

- Client secret no frontend — viola a spec.
- Commitar `.env` de lab — viola constituição e `.gitignore` já existente.

## 9. Testes (tipos que o projeto já usa)

**Decision**: Sem E2E. Backend unidade: Services de identidade e de tarefa com repositório e provedor mockados. Backend feature: rotas 401, isolamento A/B, callback com fake, logout, órfãs. Frontend unidade: `authApi`/`taskApi` (método, payload, `credentials`). Frontend integração: `AuthContext` com API mockada. Frontend componente: `SignInScreen` (botão, mensagem de cancelamento) e casca autenticada (nome/e-mail e sair). Fake do provedor cobre sucesso, cancelamento e falha.

**Rationale**: Constituição II e `AGENTS.md`. Cancelar no Google não precisa abrir o Google de verdade.

**Alternatives considered**:

- Teste E2E com Google real — proibido neste projeto e instável.
- Http fake do Socialite em todo teste de unidade — acopla teste à biblioteca; a interface fake é o ponto certo.
