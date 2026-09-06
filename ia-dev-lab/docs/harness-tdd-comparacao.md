# Etapa 2. Comparação: com TDD versus sem TDD

Data: 2026-09-06  
Branch: `feature/tdd-guardrail`

| Dimensão | Tarefa A (filtro, com TDD) | Tarefa B (editar título, sem TDD) |
| --- | --- | --- |
| Ordem | Teste → falha → código | Código direto |
| Evidência | Commits `49ca1d1` (Red) e `48a55fe` (Green) | Sem commit Red; feature sem testes novos |
| Qualidade da regra | Filtro no Service, status validado, UI só dispara | Service e Request ok, mas sem prova de isolamento |
| Casos de borda | Status inválido coberto (422); pending/done cobertos | Título vazio depende só da Request sem teste da rota; sem teste de dono |
| Tempo | Maior (dois passos + Docker no Red) | Menor no caminho feliz |
| Risco residual | Baixo para o escopo do filtro | Médio: regressão do rename pode passar despercebida |
| Aderência ao AGENTS.md | Alta (TDD explícito) | Parcial (camadas ok, TDD ignorado de propósito) |

## Conclusão

Com TDD, a feature nasceu com contrato executável e histórico auditável. Sem TDD, o código “parece” alinhado ao padrão do lab, mas a suite existente dá falsa segurança. Para o harness, a tarefa A é o modelo a repetir; a B serve só como contraste.

## Artefatos

- Com TDD: `docs/harness-tdd-com.md`
- Sem TDD: `docs/harness-tdd-sem.md`
