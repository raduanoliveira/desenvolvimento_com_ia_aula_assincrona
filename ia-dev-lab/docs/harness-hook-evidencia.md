# Harness Etapa 1. Hook contra destruição do banco

Data: 2026-09-06  
Branch: `feature/prazo-plan`  
Risco escolhido (diferente do exemplo “bloquear merge na main”): **destruir o banco do `ia-dev-lab`**.

## O que o hook bloqueia

Arquivos:

- `.cursor/hooks.json`
- `.cursor/hooks/block-db-destruction.mjs`
- `.cursor/hooks/test-block-db-destruction.mjs` (suite local de evidência)

Eventos:

- `beforeShellExecution` (failClosed)
- `preToolUse` com matcher `Delete|Shell` (failClosed)

Padrões negados:

- `migrate:fresh`, `migrate:refresh`, `migrate:reset`
- `db:wipe`, `schema:drop`
- remoção de `database.sqlite` (shell ou tool Delete)

Comandos seguros como `migrate` e `php artisan test` continuam liberados.

Observação Windows: o script remove BOM UTF-8 do stdin antes do `JSON.parse`, para o guardrail não falhar em aberto.

## Evidência 1. Suite local (stdin simulado)

Comando usado (sem padrões proibidos na linha do Shell do agente):

```text
node .cursor/hooks/test-block-db-destruction.mjs
```

Resultado gravado em `docs/harness-hook-evidencia-log.txt`:

```text
deny-artisan-fresh: expect=deny got=deny ok=true
deny-db-wipe: expect=deny got=deny ok=true
deny-schema-drop: expect=deny got=deny ok=true
deny-remove-sqlite: expect=deny got=deny ok=true
deny-delete-tool: expect=deny got=deny ok=true
allow-migrate: expect=allow got=allow ok=true
allow-test: expect=allow got=allow ok=true
deny-with-bom: expect=deny got=deny ok=true
ALL_CASES_PASSED
```

## Evidência 2. Bloqueio real no Cursor (agente)

Disparei de propósito, via tool Shell do agente, o comando:

```text
Write-Output 'harness-probe migrate:fresh'
```

O Cursor **recusou** a tool com a mensagem do hook (não chegou a executar no terminal):

```text
Rejected: Bloqueado pelo harness: esta ação pode destruir o banco do ia-dev-lab (migrate:fresh, db:wipe, schema:drop ou remoção de .sqlite). Use migrate normal ou peça autorização humana explícita.
```

Isso confirma que o controle impede a ação de risco no fluxo real do agente, não só no teste unitário do script.

## Conclusão

O guardrail está commitado no repositório e foi testado de duas formas: fixtures locais e tentativa real bloqueada pelo Cursor.
