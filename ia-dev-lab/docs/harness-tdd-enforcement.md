# Etapa 2. Investigação: Superpowers (enforcement de TDD)

Data: 2026-09-06  
Ferramenta escolhida: **somente Superpowers** ([obra/superpowers](https://github.com/obra/superpowers)).  
Não usei `tdd-guard` nesta etapa.

## O que é

Superpowers é um framework de skills e workflow para agentes de código. Em vez de um hook isolado que bloqueia escrita de arquivo, ele empilha skills obrigatórias: brainstorming, plano, worktrees, subagentes e, no miolo da implementação, a skill **test-driven-development**.

A skill de TDD declara a “Iron Law”: **não há código de produção sem um teste falhando antes**. O ciclo documentado é Red (escrever e ver falhar) → Green (mínimo para passar) → Refactor (sem adicionar comportamento). Código escrito antes do teste deve ser apagado e refeito.

## Instalação no Cursor

A documentação oficial indica, no Agent chat do Cursor:

```text
/add-plugin superpowers
```

Ou buscar “superpowers” no marketplace de plugins do Cursor.

Neste ambiente da prática, a instalação via marketplace depende da ação no próprio Cursor (UI/comando de plugin). Não há pacote npm do laboratório que “ative” o Superpowers sozinho no `ia-dev-lab`. Por isso registro aqui o comportamento esperado a partir da documentação oficial e do encaixe com este projeto.

## Como se comportaria no cenário do ia-dev-lab

No fluxo Superpowers, ao pedir a feature de filtro por status (tarefa A), o agente deveria:

1. **Brainstorm / design** curto antes de editar arquivos.
2. **Plano** com microtarefas (arquivos e critério de verificação).
3. Na implementação, a skill **test-driven-development** forçaria:
   - escrever `ListTasksServiceTest` (e/ou feature) primeiro;
   - rodar `docker compose exec backend php artisan test` e ver vermelho;
   - só então alterar `ListTasksService`, Request e controller;
   - repetir o mesmo no frontend (`taskApi` / `TaskStatusFilter`) antes do componente.

Se o agente tentasse “já implementar o Service e testar depois”, a skill orienta a **parar e deletar** o código de produção, porque testes feitos depois passam de imediato e não provam o Red.

Diferença prática em relação a um hook mecânico (como tdd-guard): Superpowers **persuade/obriga via skill e workflow**, não necessariamente intercepta cada `Write` com um validador externo. No Cursor, a eficácia depende do plugin estar instalado e da skill ser disparada no início da sessão. Ainda assim, no meu cenário, o protocolo Red-Green documentado no Superpowers é exatamente o que usei de forma deliberada na tarefa A (commits `49ca1d1` e o commit Green seguinte).

## Conclusão da investigação

- **Escolha:** Superpowers, alinhada ao enunciado (uma ferramenta de enforcement).
- **Instalação:** prevista via `/add-plugin superpowers` no Cursor; documentada a partir do README oficial.
- **Encaixe no lab:** combina com Docker + PHPUnit/Vitest; a skill de TDD casa com o `AGENTS.md` (“teste primeiro”).
- **Limite:** sem o plugin ativo na sessão, o enforcement não é automático; o humano (ou o prompt) precisa seguir o ritual. Por isso a evidência forte da etapa continua sendo o histórico Red → Green na branch.
