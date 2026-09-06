# Etapa 6. Opção B: custo e performance (tokens)

Data: 2026-09-06  
Projeto: `ia-dev-lab`  
Modelo nas três features: Composer.

O Cursor não expõe, nestes transcripts, a fatura cobrada por chat. A estimativa abaixo usa o log da ferramenta: caracteres gravados no JSONL das sessões, divididos por 4. Isso mede o que ficou no transcript (falas, ferramentas e resultados), não o contexto completo reenviado a cada turno. Os números são ordem de grandeza justificada, não invoice.

Sessões:

- [Aula 6, Etapas 1 e 2](0578e80a-af46-48e8-bf9e-b8bd86e02304)
- [Superpowers e relatório](e70a0b16-cea8-4c85-a3fa-0be6786534fc)

## Recorte das três tarefas

| Tarefa | Janela (6 set 2026) | Perguntas | Ferramentas | Tokens no log (chars ÷ 4) |
| --- | --- | --- | --- | --- |
| Prazo (Auto + Plan + hook) | 12:01–13:30 | 19 | 249 | ~58 mil |
| Filtro + editar título | 13:31–14:05 | 7 | 100 | ~22 mil |
| Prioridade (Superpowers) | 14:07–14:30 | 4 | 181 | ~22 mil |

A tarefa recente da opção B é a **prioridade**. As outras duas já tinham passado nesta prática: prazo (Etapa 1) e filtro com TDD mais edição de título sem TDD (Etapa 2).

Fora dessas features, o relatório e as Etapas 3 a 5 (14:35–15:40) gravaram ~61 mil tokens no mesmo método — mais que qualquer feature isolada.

## O que a comparação mostrou

A prioridade saiu no mesmo patamar do filtro (~22 mil), com um commit quase do tamanho do prazo Plan (`0b75d75`, +478/−73). O que mudou foi a densidade: 45 chamadas de ferramenta por pergunta, contra cerca de 13 no prazo e 14 no filtro. Poucas falas minhas; o agente concentrou TDD, migration e testes.

O prazo parece o dobro porque não foi uma execução: foram Auto, Plan e o hook. Esse custo é do harness da Etapa 1, não do campo `due_date` sozinho.

Chamadas: a prioridade teve 4 turnos meus e 181 ferramentas. O prazo, 19 turnos e 249 ferramentas, inflado pela duplicação e pela documentação da Etapa 1.

## Estratégia de otimização

**Model routing.** Composer permanece na feature (TDD, Docker, schema). Ajustes de LaTeX, margem e tabela vão para um modelo menor. Cache de prompt no `AGENTS.md` renderia pouco neste recorte: o custo visível está nas ferramentas e na reescrita do relatório, não no contexto fixo. Batch API não se aplica a um único desenvolvedor neste laboratório.

## O que se aprendeu

Medir só a última feature esconde o padrão. Duplicar a mesma tarefa para comparar modos (Etapa 1) custa mais tokens do que acrescentar uma coluna com Superpowers. Reescrever o PDF custa mais do que o filtro. O controle de custo, neste harness, é escolher o modelo certo para o tipo de turno, não enxugar o `AGENTS.md`.
