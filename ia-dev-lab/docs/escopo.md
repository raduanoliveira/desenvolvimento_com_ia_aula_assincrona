# Escopo da prática SDD

Projeto: o mesmo ToDo do `ia-dev-lab` (API Laravel, tela React, Docker). Ainda não existe autenticação. A lista de tarefas é global e anônima.

## Funcionalidades escolhidas

1. **Entrar com Google.** A pessoa autentica com a conta Google e passa a ver só as próprias tarefas.
2. **Entrar com GitHub.** A pessoa autentica com a conta GitHub e passa a ver só as próprias tarefas.

As duas são parecidas de propósito: mesmo problema de produto (identidade + dono da tarefa), provedores diferentes. Isso deixa a comparação entre ferramentas de SDD honesta — o que muda é o método de especificar, não o tamanho da feature.

## Por que são boas candidatas para SDD

Login com provedor externo tem regra de negócio (quem é o usuário, o que ele pode ver), vários arquivos (API, persistência, tela, sessão) e casos de borda que a IA não inventa sozinha: cancelar o consentimento, pedido sem sessão, um usuário tentando mexer na tarefa de outro, dois provedores da mesma pessoa. Sem spec, o modelo costuma colocar a regra no controller, pular teste e deixar a lista ainda global. Com spec, dá para exigir isolamento por usuário, recusa de acesso anônimo e checkpoint humano antes da migração que muda o modelo de dados.

## Como cada ferramenta entra

- **GitHub Spec Kit** especifica e implementa o **login com Google** (primeira feature: cria o domínio de identidade).
- **OpenSpec** especifica e implementa o **login com GitHub** (segunda feature: delta em cima da identidade que já existe).

O registro do porquê de cada uma está em `docs/sdd-ferramentas.md`.

## Fora deste escopo

- Vincular a mesma pessoa entre Google e GitHub (são identidades separadas).
- Cadastro com e-mail e senha.
- Recuperação de senha, 2FA, papéis de administrador.
- Tornar pública a lista de outro usuário.
