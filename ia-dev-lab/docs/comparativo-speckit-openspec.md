# Comparativo Spec Kit × OpenSpec (prática SDD)

Avaliação a partir do que eu vivi no `ia-dev-lab`: login Google (Spec Kit) e login GitHub (OpenSpec).  
A coluna **Preferência** indica quem se saiu melhor **naquele eixo**, ou empate quando os dois atendem igualmente (ou cada um em um cenário).

Legenda: **SK** = Spec Kit · **OS** = OpenSpec · **=** = empate / depende do cenário

| Dimensão | Spec Kit (Google) | OpenSpec (GitHub) | Preferência |
|---|---|---|---|
| Melhor cenário | Feature que cria domínio novo (identidade, sessão, dono) | Delta em brownfield (mais um provedor) | **=** (cada um no seu) |
| Onde a spec vive | `specs/001-google-sign-in/` (feature completa) | `openspec/changes/login-com-github/` (delta ADDED) | **=** |
| Fluxo até o código | constitution → specify → plan → tasks → implement | propose → apply (archive depois) | **SK** se a turma precisa ver todas as etapas; **OS** se o objetivo é mudança rápida |
| Granularidade das tarefas | ~70 tarefas bem fatiadas | ~8 grupos, forte reuso | **SK** para aprendizado e revisão item a item; **OS** para ritmo em brownfield |
| Controle humano / checkpoint | T030 explícito antes da migration de dono | Sem T030 de tarefas (migration só em `users`); risco de ir rápido demais | **SK** |
| Papel do `AGENTS.md` | Instaurou o padrão (TDD, Service, Repository, Docker) ao criar o domínio | Protegeu o padrão: Service novo, Google intacto, sem unir por e-mail | **=** (papéis diferentes, ambos fortes) |
| Risco se a spec for fraca | Lista global / regra no controller OAuth | Unir contas por e-mail ou inchar o Service do Google | **=** |
| Código final vs requisitos | Atendeu (duas contas Google no browser) | Atendeu (GitHub + isolamento Google↔GitHub) | **=** |
| Respeito ao `AGENTS.md` no código | Sim (auditoria em `conformidade-agents.md`) | Sim (idem) | **=** |
| Curva de entrada nesta prática | Mais comandos e artefatos; mais didático | Poucos comandos; delta mais direto | **OS** para começar uma mudança pequena; **SK** para a primeira feature do domínio |

## Leitura rápida

- **Não há vencedor absoluto.** Spec Kit venceu onde eu precisei de freio humano e de decomposição longa (checkpoint, 70 tasks). OpenSpec venceu onde o domínio já existia e eu só acrescentei o GitHub.
- No eixo **`AGENTS.md`**, empate consciente: um **instaura**, o outro **protege**. Não é o mesmo trabalho.
- Para a disciplina: usar **Spec Kit** na primeira feature do laboratório e **OpenSpec** na segunda foi a combinação certa.

## Fonte

- Spec Kit: `specs/001-google-sign-in/`
- OpenSpec: `openspec/changes/login-com-github/`
- Conformidade: `docs/conformidade-agents.md`
