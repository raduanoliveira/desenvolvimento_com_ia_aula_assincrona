#!/usr/bin/env node
/**
 * Harness Etapa 1: bloqueia ações que destroem o banco do ia-dev-lab.
 * Risco diferente do exemplo de aula (bloquear merge na main).
 *
 * Eventos: beforeShellExecution, preToolUse (Delete/Shell).
 * Windows: remove BOM UTF-8 do stdin antes do JSON.parse.
 */

import fs from "fs";

const DENY_MESSAGE =
  "Bloqueado pelo harness: esta ação pode destruir o banco do ia-dev-lab (migrate:fresh, db:wipe, schema:drop ou remoção de .sqlite). Use migrate normal ou peça autorização humana explícita.";

function readInput() {
  const raw = fs.readFileSync(0, "utf8").replace(/^\uFEFF/, "");
  if (!raw.trim()) {
    return {};
  }
  return JSON.parse(raw);
}

function collectText(payload) {
  const parts = [];
  if (typeof payload.command === "string") {
    parts.push(payload.command);
  }
  if (payload.tool_input && typeof payload.tool_input === "object") {
    const input = payload.tool_input;
    for (const key of ["command", "path", "file_path", "target_file", "working_directory"]) {
      if (typeof input[key] === "string") {
        parts.push(input[key]);
      }
    }
  }
  if (typeof payload.path === "string") {
    parts.push(payload.path);
  }
  if (typeof payload.tool_name === "string") {
    parts.push(payload.tool_name);
  }
  return parts.join("\n");
}

function looksLikeSqliteDbPath(text) {
  return /database\.sqlite\b/i.test(text);
}

function isDestructiveDatabaseAction(text) {
  const artisanPatterns = [
    /migrate:fresh\b/i,
    /migrate:refresh\b/i,
    /migrate:reset\b/i,
    /\bdb:wipe\b/i,
    /schema:drop\b/i,
  ];
  if (artisanPatterns.some((re) => re.test(text))) {
    return true;
  }

  const deleteVerb =
    /\b(?:rm|del|erase|unlink|Remove-Item|\bri\b)\b/i.test(text) ||
    /Delete-Item/i.test(text);

  return deleteVerb && looksLikeSqliteDbPath(text);
}

function isDeleteOfSqlite(payload, text) {
  const toolName = String(payload.tool_name || payload.toolName || "");
  if (!/Delete/i.test(toolName)) {
    return false;
  }

  const pathValue =
    payload.path ||
    payload.tool_input?.path ||
    payload.tool_input?.file_path ||
    payload.tool_input?.target_file ||
    "";

  return /database\.sqlite\b/i.test(String(pathValue)) || /database\.sqlite\b/i.test(text);
}

function deny() {
  process.stdout.write(
    JSON.stringify({
      permission: "deny",
      user_message: DENY_MESSAGE,
      agent_message: DENY_MESSAGE,
    })
  );
}

function allow() {
  process.stdout.write(JSON.stringify({ permission: "allow" }));
}

function main() {
  let payload;
  try {
    payload = readInput();
  } catch (error) {
    process.stderr.write(`block-db-destruction: JSON inválido: ${error.message}\n`);
    deny();
    process.exit(2);
  }

  const text = collectText(payload);
  if (isDestructiveDatabaseAction(text) || isDeleteOfSqlite(payload, text)) {
    process.stderr.write(
      `block-db-destruction: DENY -> ${text.replace(/\s+/g, " ").slice(0, 200)}\n`
    );
    deny();
    process.exit(0);
  }

  allow();
  process.exit(0);
}

main();
