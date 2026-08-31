## 1. Configuração de ambiente

- [x] 1.1 Adicionar `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` (vazios) e `GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback` em `backend/.env.example` e confirmar que o arquivo versionado não contém valores secretos reais
- [x] 1.2 Espelhar as chaves vazias no `backend/.env` local (não versionar segredos) e confirmar com `grep` que `GITHUB_CLIENT_SECRET` não aparece em arquivos rastreados pelo git além de nomes de variável
- [x] 1.3 Incluir o bloco `github` em `backend/config/services.php` lendo `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` e `GITHUB_REDIRECT_URI` e confirmar que o arquivo só referencia `env()`, sem literais secretos
- [x] 1.4 Incluir dummies `GITHUB_*` em `backend/phpunit.xml` (como os `GOOGLE_*`) e confirmar que `docker compose exec backend php artisan test --testsuite=Unit` ainda passa

## 2. Abstração de identidade (Google continua verde)

- [x] 2.1 Renomear `IdentityUser::$googleId` para `providerUserId` em `backend/app/Identity/IdentityUser.php` e atualizar `FakeIdentityProvider`, `SocialiteIdentityProvider`, `CompleteGoogleSignInService` e `backend/tests/Unit/CompleteGoogleSignInServiceTest.php`; verificar com `docker compose exec backend php artisan test --filter=CompleteGoogleSignInServiceTest`
- [x] 2.2 Parametrizar `SocialiteIdentityProvider` com o driver (`google` | `github`) no construtor e usar esse driver em `redirectUrl`/`userFromCallback`; verificar que o adapter Google continua o default usado pelo `GoogleAuthController` e que `docker compose exec backend php artisan test --filter=GoogleAuthTest` passa
- [x] 2.3 Trocar o bind único em `backend/app/Providers/AppServiceProvider.php` por binding contextual: `GoogleAuthController` → Socialite `google`, `GitHubAuthController` → Socialite `github` (o controller GitHub pode ser um stub vazio nesta tarefa); em `backend/tests/TestCase.php` ligar os dois controllers ao `FakeIdentityProvider`; verificar `docker compose exec backend php artisan test --filter=GoogleAuthTest`

## 3. Persistência de `github_id`

- [x] 3.1 Escrever teste de feature que falha em `backend/tests/Feature/GitHubUserPersistenceTest.php`: `User::factory()` com `github_id` e `google_id` null persiste; um segundo usuário Google com `google_id` continua válido. Verificar a falha (coluna inexistente / NOT NULL) com `docker compose exec backend php artisan test --filter=GitHubUserPersistenceTest`
- [x] 3.2 Criar migration nova (não reescrever a create) tornando `google_id` nullable e adicionando `github_id` string nullable unique; incluir `github_id` em `backend/app/Models/User.php` (`$fillable`); adicionar state `github()` em `backend/database/factories/UserFactory.php` (`github_id` preenchido, `google_id` null). Rodar `docker compose exec backend php artisan migrate` e o teste 3.1 até passar
- [x] 3.3 Adicionar `findByGithubId` em `backend/app/Repositories/Contracts/UserRepositoryInterface.php` e `backend/app/Repositories/EloquentUserRepository.php`; verificar com um teste unitário ou o teste do service da seção 4 que a busca não usa e-mail

## 4. Caso de uso CompleteGitHubSignInService

- [x] 4.1 Escrever testes unitários que falham em `backend/tests/Unit/CompleteGitHubSignInServiceTest.php`: (a) github_id desconhecido cria usuário só com `github_id`; (b) mesmo github_id devolve o mesmo usuário; (c) usuário Google com o mesmo e-mail NÃO é reutilizado (chama `findByGithubId`, nunca `findByGoogleId` nem busca por e-mail). Verificar que falham com `docker compose exec backend php artisan test --filter=CompleteGitHubSignInServiceTest`
- [x] 4.2 Implementar `CompleteGitHubSignInService` em `backend/app/Services/CompleteGitHubSignInService.php` (find-or-create só por `github_id`) e verificar que os testes 4.1 passam e que `CompleteGoogleSignInServiceTest` continua verde

## 5. HTTP GitHub (fake, sem internet)

- [x] 5.1 Escrever testes de feature que falham em `backend/tests/Feature/GitHubAuthTest.php`: `GET /auth/github` redireciona para a URL do fake (ex. `https://github.com/login/oauth/fake`). Verificar falha com `docker compose exec backend php artisan test --filter=GitHubAuthTest`
- [x] 5.2 Implementar `GitHubAuthController@redirect` e rotas `GET /auth/github` em `backend/app/Http/Controllers/Auth/GitHubAuthController.php` e `backend/routes/web.php`. Nos testes, rebind contextual do controller GitHub para um `FakeIdentityProvider` com `redirectUrl` GitHub. Verificar que o teste 5.1 passa e o Google não quebrou (`--filter=GoogleAuthTest`)
- [x] 5.3 Escrever testes de feature que falham: callback de sucesso cria `User` com `github_id` (sem `google_id`), abre sessão, `GET /api/session` 200 com nome/e-mail, 302 para `{FRONTEND_URL}/`; segundo callback com o mesmo github_id não duplica usuário. Verificar falha até o callback existir
- [x] 5.4 Implementar `GitHubAuthController@callback` (provider → `CompleteGitHubSignInService` → `Auth::login` → redirect), espelhando o Google sem inflá-lo. Verificar que 5.3 passa
- [x] 5.5 Escrever testes de feature que falham: fake cancelled → 302 `{FRONTEND_URL}/?signin=cancelled` sem sessão; fake error → `/?signin=error` sem sessão; corpo/location sem `GITHUB_CLIENT_SECRET`. Verificar falha
- [x] 5.6 Tratar `IdentityCancelledException` / `IdentityFailedException` no callback GitHub (sem segredos no redirect) e verificar que 5.5 passa

## 6. Isolamento de tarefas (dono já existente)

- [x] 6.1 Escrever teste de feature que falha em `backend/tests/Feature/GitHubTaskIsolationApiTest.php`: usuário Google e usuário GitHub com o **mesmo e-mail** são dois `User`; tarefas do Google não aparecem na listagem GitHub; PATCH/DELETE da tarefa Google como GitHub → 404 e a tarefa permanece. Verificar falha se alguém unir por e-mail; depois da seção 4 deve passar quando os dois usuários forem linhas distintas. Rodar `docker compose exec backend php artisan test --filter=GitHubTaskIsolationApiTest`
- [x] 6.2 No mesmo arquivo, cobrir duas contas GitHub (`User::factory()->github()`): B não vê nem altera a tarefa de A; A conclui a própria. Verificar com o mesmo comando da 6.1
- [x] 6.3 Reexecutar `docker compose exec backend php artisan test --filter=TaskApiTest` e `--filter=SessionApiTest` e confirmar que sem sessão a API continua 401 (listar/criar/alterar/sessão)

## 7. Tela de entrada (TDD frontend)

- [x] 7.1 Escrever teste que falha em `frontend/src/features/auth/authApi.test.ts` garantindo `githubStartUrl` apontando para `/auth/github` (mesmo host que o Google, sem `/api`). Verificar com `docker compose exec frontend npm test -- src/features/auth/authApi.test.ts`
- [x] 7.2 Exportar `githubStartUrl` em `frontend/src/features/auth/authApi.ts` e verificar que 7.1 passa
- [x] 7.3 Atualizar testes de `SignInScreen`, `AuthContext`, `App` e `DashboardLayout` para incluir `githubStartUrl` no mock; em `SignInScreen.test.tsx` exigir os dois links (Google e GitHub), ausência de lista, e após erro/cancelamento os dois botões continuarem visíveis. Verificar que os testes de componente falham até a UI existir: `docker compose exec frontend npm test -- src/features/auth/SignInScreen.test.tsx`
- [x] 7.4 Expor `githubStartUrl` em `AuthContext` e implementar o botão **Continuar com GitHub** em `SignInScreen` (MUI, junto com Google, href `/auth/github`). Ajustar o texto de apoio da tela para mencionar as duas contas. Verificar que 7.3 passa
- [x] 7.5 Escrever/ajustar testes em `frontend/src/features/auth/AuthContext.test.tsx`: `signin=cancelled` e `signin=error` mostram copy **neutra** (não citar só o Google); status signedOut; lista não entra no probe. Verificar falha enquanto a copy antiga permanecer
- [x] 7.6 Atualizar `messageFromSignInQuery` (e fallback de erro de rede) em `frontend/src/features/auth/AuthContext.tsx` para mensagens que servem Google e GitHub. Verificar `docker compose exec frontend npm test -- src/features/auth/AuthContext.test.tsx`
- [x] 7.7 Atualizar `frontend/src/App.test.tsx` para, sem sessão, exigir Continuar com GitHub além de Google, sem lista. Verificar `docker compose exec frontend npm test -- src/App.test.tsx`

## 8. Superfície de segredos e suíte completa

- [x] 8.1 Revisar tela, mensagens e repositório: nenhum valor de `GITHUB_CLIENT_ID`/`SECRET` na SPA, em fixtures versionadas ou em asserts de redirect; `backend/.env.example` só com placeholders. Confirmar com busca no repo (`GITHUB_CLIENT_SECRET` só como nome de variável)
- [x] 8.2 Rodar `docker compose exec backend php artisan test` (Unit + Feature) e `docker compose exec frontend npm test` e confirmar tudo verde, inclusive `GoogleAuthTest` e isolamento já existente
