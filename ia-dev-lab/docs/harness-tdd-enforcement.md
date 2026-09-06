# Etapa 2. Superpowers: instalação real e uso

Data: 2026-09-06  
Ferramenta: **somente Superpowers** ([obra/superpowers](https://github.com/obra/superpowers)).  
Não usei `tdd-guard`.

## Instalação feita nesta máquina

Instalei o plugin **de fato** como plugin local do Cursor (método recomendado quando o marketplace trava ou quando se quer a versão atual do repositório):

| Item | Valor |
| --- | --- |
| Caminho | `C:\Users\Raduan\.cursor\plugins\local\superpowers` |
| Versão | `6.3.0` (plugin.json) |
| Commit | `b36e082` (*Release v6.3.0*) |
| Skills | 14 pastas em `skills/` |
| Hooks Cursor | `hooks/hooks-cursor.json` (`sessionStart`) |

Comando usado:

```text
git clone --depth 1 https://github.com/obra/superpowers.git %USERPROFILE%\.cursor\plugins\local\superpowers
```

Skills presentes: `brainstorming`, `dispatching-parallel-agents`, `executing-plans`, `finishing-a-development-branch`, `receiving-code-review`, `requesting-code-review`, `subagent-driven-development`, `systematic-debugging`, `test-driven-development`, `using-git-worktrees`, `using-superpowers`, `verification-before-completion`, `writing-plans`, `writing-skills`.

Complemento oficial do marketplace (recomendado na mesma máquina): no Agent chat do Cursor, rodar também:

```text
/add-plugin superpowers
```

(ou `/plugin-add superpowers`, conforme a versão do Cursor).

## Ativação na sessão

O Superpowers só passa a orientar o agente **depois** que o Cursor carrega o plugin (em geral: reiniciar o Cursor ou abrir um **Agent chat novo**). O hook `sessionStart` injeta o bootstrap da skill `using-superpowers`.

Como verificar:

```text
Do you have superpowers?
```

O agente deve listar as skills e dizer que vai usá-las antes de agir.

## O que “usar na íntegra” significa neste projeto

No fluxo completo do Superpowers, para features do `ia-dev-lab`:

1. `using-superpowers` / `brainstorming` antes de desenhar.
2. `writing-plans` com microtarefas e verificação.
3. `test-driven-development` (Red → Green → Refactor) em cada implementação.
4. `verification-before-completion` antes de declarar pronto.
5. `finishing-a-development-branch` ao fechar a branch.

A tarefa A (filtro) desta etapa já deixou evidência Red/Green nos commits `49ca1d1` e `48a55fe`. Com o plugin agora instalado no disco, a **próxima sessão de Agent** deve seguir esse fluxo com as skills carregadas de verdade (não só imitando o ritual).

## Limite honesto da sessão anterior

Antes deste clone local, a etapa tinha só documentação da ferramenta. A partir deste arquivo, a instalação no disco está feita; o uso automático das skills exige Agent chat novo (ou reinício) com o plugin reconhecido pelo Cursor.
