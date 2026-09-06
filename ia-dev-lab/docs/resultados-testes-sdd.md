# Resultados dos testes — prática SDD

Data da bateria registrada: **31/08/2026**. Ambiente: Docker Compose em `ia-dev-lab` (backend `:8000`, frontend `:5173`).

Ferramentas sob teste: **Spec Kit** (login Google) e **OpenSpec** (login GitHub).

## 1. Testes automatizados (Docker)

Comandos:

```bash
docker compose exec backend php artisan test
docker compose exec frontend npm test -- --run
```

| Suite | Resultado |
|---|---|
| Backend (PHPUnit Unit + Feature) | **49 passed** (181 assertions) |
| Frontend (Vitest) | **23 passed** (9 arquivos) |

### Spec Kit / Google (amostra)

- Unit: `CompleteGoogleSignInServiceTest` (cria usuário, reencontra, não toca tarefas)
- Feature: `GoogleAuthTest` (redirect, callback, cancelamento, erro, sem vazar secret)
- Feature: `SessionApiTest`, `TaskIsolationApiTest`, `TaskOrphanApiTest`, `TaskUnauthenticatedApiTest` (401 sem sessão)

### OpenSpec / GitHub (amostra)

- Unit: `CompleteGitHubSignInServiceTest` — **não reutiliza usuário Google com o mesmo e-mail**
- Feature: `GitHubAuthTest` (redirect, callback, cancelamento, erro, sem vazar secret)
- Feature: `GitHubTaskIsolationApiTest` (Google↔GitHub mesmo e-mail; duas contas GitHub)
- Feature: `GitHubUserPersistenceTest` (`github_id` com `google_id` null)

### Frontend (ambos)

- `SignInScreen`: Continuar com Google e Continuar com GitHub; sem lista deslogado
- `AuthContext`: sessão, sair, mensagens `signin=cancelled` / `signin=error`
- `App`: gate entrada vs dashboard; `TaskProvider` só com sessão

**Conclusão automatizada:** as duas specs foram atendidas nos testes derivados dos critérios Given/When/Then, com provedor **fake** (sem internet).

## 2. Smoke test HTTP (ao vivo)

| Checagem | Resultado |
|---|---|
| `GET /api/session` sem cookie (`Accept: application/json`) | **401** `Não autenticado.` |
| `GET /api/tasks` sem cookie | **401** `Não autenticado.` |
| `GET /auth/google` | **302** → `accounts.google.com` (callback `.../auth/google/callback`, escopos openid/profile/email) |
| `GET /auth/github` | **302** → `github.com/login/oauth/authorize` (callback `.../auth/github/callback`, scope `user:email`) |
| Credenciais no container (só presença) | Google **ok**, GitHub **ok** |
| Frontend `http://localhost:5173` | **200** |

## 3. Testes manuais no browser

| Cenário | Ferramenta | Resultado |
|---|---|---|
| Duas contas Google, listas separadas, sair | Spec Kit | **OK** (validado na prática) |
| Continuar com GitHub, lista pessoal, sair | OpenSpec | **OK** (validado após preencher `.env` e recriar o backend) |
| Tela de entrada mostra os dois botões | Ambas | **OK** |

Isolamento “mesmo e-mail Google e GitHub” ficou coberto de forma forte pelos testes automatizados (`GitHubTaskIsolationApiTest`); no browser basta entrar com cada provedor e conferir listas distintas.

## 4. O que isso diz sobre as ferramentas

- **Spec Kit:** o plano longo e o T030 geraram testes de órfãs, 401 e isolamento A/B que passaram nesta bateria.
- **OpenSpec:** o delta e o teste de “não unir por e-mail” passaram; o Google continuou verde depois do apply (`GoogleAuthTest` incluso na suíte completa).

Detalhe da comparação de processo: `docs/comparativo-speckit-openspec.md`.  
Conformidade com `AGENTS.md`: `docs/conformidade-agents.md`.
