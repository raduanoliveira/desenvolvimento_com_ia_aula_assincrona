# 0003. Ambiente só com Docker

## Status

Aceita.

## Contexto

O laboratório usa PHP, Node e banco. Instalar tudo no Windows geraria versões diferentes, caminho quebrado e um passo a passo difícil para a IA repetir.

## Decisão

Eu subi o backend e o frontend só com Docker Compose. PHP, Node e banco não são instalados na máquina.

## Por quê

Eu queria a máquina limpa, sem instalar nada extra no Windows, e um jeito único da IA operar o ambiente pelos comandos do arquivo de contexto. Subir e parar tudo vira um comando só, sem receita diferente para cada ferramenta.

## Consequências

Quem for trabalhar neste projeto precisa do Docker Desktop. Os testes e o servidor rodam dentro dos containers. A IA não deve sugerir instalar PHP ou Node no host.
