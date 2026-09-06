# Quickstart: validar entrar com Google

Guia de validação no ambiente Docker. Não substitui `tasks.md`. Sem E2E automatizado: os testes abaixo são os do repositório; o fluxo no browser é checagem manual opcional depois dos testes.

## Pré-requisitos

- Docker Compose na pasta do laboratório (`ia-dev-lab`)
- Spec: [spec.md](./spec.md)
- Contratos: [contracts/auth.md](./contracts/auth.md), [contracts/tasks.md](./contracts/tasks.md), [contracts/ui.md](./contracts/ui.md)
- Modelo: [data-model.md](./data-model.md)

PHP, Node e SQLite **não** se instalam no host.

## Ambiente

```bash
docker compose up --build -d
```

Credenciais Google só em `.env` do backend (já ignorado pelo git). Copiar de exemplo e preencher:

- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI` (callback do contrato, ex. `http://localhost:8000/auth/google/callback`)
- `FRONTEND_URL=http://localhost:5173`

Nunca colar secret em `plan.md`, teste, mensagem de erro ou frontend.

## Checkpoint humano (obrigatório)

**Pare antes** de aplicar a migration que adiciona `user_id` em `tasks`.

Checklist da revisão:

- [ ] Tarefas atuais ficam com `user_id` nulo (sem backfill)
- [ ] Coluna permanece nullable (não exige dono no schema)
- [ ] Quem autenticar não herda a lista anônima
- [ ] Decisão: aprovar / editar o plano / voltar à spec

Só depois da aprovação: `docker compose exec backend php artisan migrate`.

A migration de `users` pode existir antes deste ponto; a de dono em `tasks` não.

## Testes (prova da feature)

Provedor Google **fake** no PHPUnit. Frontend mocka `authApi` / `taskApi`. Nada fala com a internet.

```bash
docker compose exec backend php artisan test --testsuite=Unit
docker compose exec backend php artisan test --testsuite=Feature
docker compose exec frontend npm test
```

Esperado: verde. Feature tests cobrem 401 sem sessão, isolamento A/B, órfãs invisíveis, callback cancelado e logout. Ver [research.md](./research.md) tipos de teste.

## Cenários manuais (opcional, depois dos testes)

Frontend: `http://localhost:5173` · API: `http://localhost:8000`.

1. Sem sessão: só tela de entrada; Continuar com Google visível; sem lista.
2. Google confirma: dashboard com lista vazia (primeira vez) ou só as próprias; nome/e-mail na casca.
3. Criar tarefa; em outro browser/conta Google a tarefa não aparece; pedido no id alheio não altera.
4. Sair: volta à entrada; `GET /api/tasks` sem cookie → 401.
5. Continuar com Google e cancelar: mensagem clara, lista fechada, botão ainda visível.
6. Tarefas antigas no SQLite sem dono: autenticar não as mostra.

## Fora deste guia

Implementação, corpos de Service e suíte completa ficam em `/speckit-tasks` e na implementação. Este arquivo só valida que o desenho é executável no Docker do lab.
