# Contract: telas

Layout MUI moderno (não tela crua). A tela não chama a API; lê `AuthContext` / `TaskContext`.

## Tela de entrada (sem sessão)

- Não renderiza lista, resumo nem formulário de nova tarefa
- Card com ação primária **Continuar com Google** (navega para `GET /auth/google`)
- Se a URL tiver `signin=cancelled` ou `signin=error`, `Alert` com a mensagem do contrato de auth
- Depois do alerta, o botão continua disponível

## Dashboard autenticado (com sessão)

- Layout atual da lista (form, lista, resumo) permanece
- AppBar mostra nome ou e-mail da sessão (FR-014) no lugar do avatar genérico “T”
- Ação **Sair** chama `DELETE /api/session` via `AuthContext` e volta à tela de entrada
- `TaskProvider` só existe neste estado; não dispara `GET /api/tasks` na tela de entrada

## Casca / roteamento na SPA

Não há React Router obrigatório: `App` (ou um gate acima) escolhe entrada vs dashboard conforme `GET /api/session`. Query `signin=*` é lida uma vez, a mensagem vai para o estado de auth, e pode ser limpa da URL sem recarregar a lista.
