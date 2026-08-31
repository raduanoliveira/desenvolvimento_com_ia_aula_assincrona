# Contract: identidade e sessão

Base da API: `http://localhost:8000`. Rotas de redirect OAuth são navegação do browser (não JSON). Rotas `/api/*` são JSON e enviam cookie de sessão (`credentials: include`).

Variáveis de ambiente (nunca no repositório): `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `FRONTEND_URL`.

## GET `/auth/google`

Inicia continuar com Google.

- **Auth**: nenhuma
- **Resposta**: `302` para o Google (URL gerada pelo `IdentityProviderInterface`)
- A tela só navega para este endereço; não faz `fetch`

## GET `/auth/google/callback`

Retorno do Google. O controller delega ao Service de completar entrada. O Service usa o provedor abstrato (fake nos testes).

### Sucesso

- Identidade confirmada → find-or-create `User` por `google_id` → abre sessão Laravel
- **Não** altera tarefas
- `302` para `{FRONTEND_URL}/` (lista; a SPA lê `GET /api/session` e mostra o dashboard)

### Cancelamento ou recusa (`error` / access_denied)

- Não abre sessão
- `302` para `{FRONTEND_URL}/?signin=cancelled`

### Falha do provedor (erro / indisponível)

- Não abre sessão
- `302` para `{FRONTEND_URL}/?signin=error`
- Corpo de redirect **sem** secret, **sem** stack trace de credencial

## GET `/api/session`

Quem está na sessão.

- Com sessão: `200`

```json
{
  "data": {
    "id": 1,
    "name": "Ana Silva",
    "email": "ana@example.com"
  }
}
```

- Sem sessão: `401` com JSON de erro genérico, **sem** lista de tarefas e **sem** segredos

```json
{
  "message": "Não autenticado."
}
```

## DELETE `/api/session`

Encerra a sessão.

- `204` sem corpo
- Pedidos seguintes a `/api/session` e `/api/tasks` → `401` até novo callback bem-sucedido
- Sem sessão prévia: ainda `204` (já desconectado)

## Mensagens na tela (contrato de produto, não payload do Google)

| Query na SPA              | Mensagem visível |
|---------------------------|------------------|
| `signin=cancelled`        | A entrada com Google não foi concluída. |
| `signin=error`            | Não foi possível entrar com o Google. Tente de novo. |

O botão Continuar com Google permanece visível.
