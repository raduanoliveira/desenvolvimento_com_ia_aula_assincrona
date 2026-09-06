# Data Model: Entrar com Google

## Entidades

### User (pessoa autenticada)

Representa quem concluiu a entrada com Google. Persistida para reencontrar a mesma pessoa na próxima visita (FR-013).

| Campo       | Tipo    | Regras |
|-------------|---------|--------|
| id          | inteiro | chave; gerado pelo sistema |
| google_id   | string  | único, obrigatório; identidade estável no Google |
| name        | string  | obrigatório; exibido na sessão (FR-014) |
| email       | string  | obrigatório; exibido na sessão (FR-014) |
| created_at  | tempo   | sistema |
| updated_at  | tempo   | sistema |

**Regras**:

- A mesma `google_id` é sempre o mesmo User (find-or-create; nunca duplicar).
- Não há senha neste modelo. Não há vínculo com GitHub nesta feature.
- Criar User **não** atribui tarefas existentes.

**Estado**: não autenticado (sem sessão) vs autenticado (sessão Laravel aponta para este User). Encerrar sessão não apaga o User.

### Session (sessão)

Não é tabela própria: sessão Laravel em cookie, guardada pelo driver já usado no ambiente (arquivo no Docker, `array` no PHPUnit).

| Dado        | Regras |
|-------------|--------|
| user_id     | presente só depois do callback Google bem-sucedido |
| validade    | até logout explícito ou expiração usual de sessão web |

**Transições**:

```text
sem sessão --[Google confirma identidade]--> autenticado
sem sessão --[cancelar / recusar / erro Google]--> sem sessão (+ mensagem na tela)
autenticado --[encerrar sessão]--> sem sessão
autenticado --[sessão expirada]--> sem sessão
```

Pedido a tarefas ou a “quem sou eu” sem sessão válida → recusa (401).

### Task (tarefa)

Modelo já existente. Esta feature acrescenta dono, sem herdar órfãs.

| Campo      | Tipo     | Regras |
|------------|----------|--------|
| id         | inteiro  | já existe |
| title      | string   | já existe; obrigatório |
| done       | boolean  | já existe |
| archived   | boolean  | já existe |
| user_id    | inteiro? | **novo, nullable**; FK para `users`; nulo = tarefa sem dono |
| timestamps | tempo    | já existe |

**Regras**:

- Tarefa **nova** nasce com `user_id` = pessoa da sessão.
- Listar: só `user_id` = pessoa da sessão **e** `archived = false` (regra atual de arquivo permanece).
- Concluir / arquivar / excluir: só se `user_id` = pessoa da sessão; senão a tarefa não é encontrada para aquele usuário.
- `user_id` nulo: invisível para qualquer autenticado; não é atualizado no login (FR-010, FR-011).

**Estados** (além de done/archived):

```text
órfã (user_id nulo)     — inacessível a autenticados; sem transição automática para “com dono”
com dono                — visível e alterável só pelo dono
```

Não há transição órfã → com dono nesta feature.

## Relacionamentos

- User 1 — N Task (apenas as que ele criou depois de autenticado).
- Task 0..1 User (`user_id` nulo permitido).
- Session 0..1 User.

## Validation rules (entrada)

| Entrada                         | Regra |
|---------------------------------|--------|
| Callback Google sucesso         | `google_id`, `name`, `email` presentes na identidade do provedor |
| Callback cancelado / recusado   | não cria User, não abre sessão |
| POST `/api/tasks`               | sessão obrigatória; `title` como hoje (obrigatório, não vazio) |
| PATCH/DELETE tarefa             | sessão obrigatória; id da tarefa; dono = sessão |
| DELETE `/api/session`           | sessão atual encerrada; idempotente se já não houver sessão (sempre volta a desconectado) |

## Checkpoint de schema

1. Migration `create_users_table` — **não** muda dono de tarefa; pode ser aplicada no fluxo normal de TDD.
2. **PARAR.** Revisão humana: impacto nas tarefas atuais (todas órfãs), confirmação de `user_id` nullable, **proibido** backfill, **proibido** `NOT NULL`.
3. Só após aprovação: migration `add_user_id_to_tasks_table`.
