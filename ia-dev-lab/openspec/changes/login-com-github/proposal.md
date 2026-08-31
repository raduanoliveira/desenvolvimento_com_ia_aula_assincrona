## Why

A lista já é pessoal via Google, mas quem prefere GitHub não consegue entrar. Esta mudança acrescenta o mesmo valor (ver e alterar só as próprias tarefas) com um segundo provedor, reusando sessão, dono e tela de entrada já existentes — e mantendo as duas identidades separadas, como o escopo da prática exige.

## What Changes

- Na tela de entrada, além de Continuar com Google, passa a existir **Continuar com GitHub**.
- Quem autentica com GitHub volta identificado e vê só as tarefas daquela conta GitHub.
- Conta Google e conta GitHub **não se unem**: mesmo e-mail em provedores diferentes continua sendo duas pessoas neste aplicativo, com listas distintas.
- Cancelar ou recusar o consentimento no GitHub deixa a pessoa desconectada, com mensagem clara; a lista não abre.
- Pedido à API sem sessão continua recusado (401). Isolamento por dono já existente permanece.
- Segredos do aplicativo GitHub (`GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, URI de callback) só em variáveis de ambiente, nunca na tela, no repositório ou em mensagem de erro.

## Capabilities

### New Capabilities

- `identity/github-sign-in`: entrar com GitHub, sessão da conta GitHub, identidade separada da conta Google, cancelamento com mensagem clara, segredos fora da superfície do produto.

### Modified Capabilities

- *(nenhuma — ainda não há specs OpenSpec arquivadas; o login com Google vive no Spec Kit, fora de `openspec/specs/`)*

## Impact

- Backend: novo fluxo OAuth GitHub (Socialite atrás da abstração de identidade já existente), Service de completar entrada GitHub, repositório de usuários com `github_id`, migration em `users` (`google_id` passa a poder ser nulo; `github_id` nullable).
- Frontend: `SignInScreen` e `authApi`/`AuthContext` ganham a ação GitHub; `TaskProvider` continua só com sessão.
- Testes: fake do GitHub (sem internet); feature de isolamento entre usuário Google e usuário GitHub; 401 e cancelamento.
- Dependência: `laravel/socialite` já está no projeto (driver GitHub).
- Fora: e-mail/senha, 2FA, admin, unir contas, lista pública.
