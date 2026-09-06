#!/usr/bin/env node
/**
 * Exercita block-db-destruction.mjs sem colocar padrões proibidos na linha de comando do Shell.
 */
import { spawnSync } from "child_process";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const hook = path.join(__dirname, "block-db-destruction.mjs");
const root = path.resolve(__dirname, "..", "..");

const cases = [
  {
    name: "deny-artisan-fresh",
    payload: { command: ["php", "artisan", "migrate" + ":fresh"].join(" ") },
    expect: "deny",
  },
  {
    name: "deny-db-wipe",
    payload: { command: ["php", "artisan", "db" + ":wipe", "--force"].join(" ") },
    expect: "deny",
  },
  {
    name: "deny-schema-drop",
    payload: { command: ["php", "artisan", "schema" + ":drop"].join(" ") },
    expect: "deny",
  },
  {
    name: "deny-remove-sqlite",
    payload: {
      command: ["Remove-Item", "ia-dev-lab/backend/database/database.sqlite"].join(" "),
    },
    expect: "deny",
  },
  {
    name: "deny-delete-tool",
    payload: {
      tool_name: "Delete",
      tool_input: { path: "ia-dev-lab/backend/database/database.sqlite" },
    },
    expect: "deny",
  },
  {
    name: "allow-migrate",
    payload: { command: "docker compose exec backend php artisan migrate" },
    expect: "allow",
  },
  {
    name: "allow-test",
    payload: { command: "docker compose exec backend php artisan test" },
    expect: "allow",
  },
  {
    name: "deny-with-bom",
    payload: { command: ["php", "artisan", "migrate" + ":fresh", "--seed"].join(" ") },
    expect: "deny",
    bom: true,
  },
];

const lines = [];
let failed = 0;

for (const testCase of cases) {
  let stdin = JSON.stringify(testCase.payload);
  if (testCase.bom) {
    stdin = "\uFEFF" + stdin;
  }

  const result = spawnSync(process.execPath, [hook], {
    input: stdin,
    encoding: "utf8",
    cwd: root,
  });

  let permission = "parse-error";
  try {
    permission = JSON.parse(result.stdout || "{}").permission;
  } catch {
    permission = `stdout=${JSON.stringify(result.stdout)}`;
  }

  const ok = permission === testCase.expect;
  if (!ok) {
    failed += 1;
  }

  const line = `${testCase.name}: expect=${testCase.expect} got=${permission} ok=${ok}`;
  lines.push(line);
  console.log(line);
  if (result.stderr) {
    lines.push(`  stderr: ${result.stderr.trim()}`);
  }
}

const evidencePath = path.join(root, "ia-dev-lab", "docs", "harness-hook-evidencia-log.txt");
fs.mkdirSync(path.dirname(evidencePath), { recursive: true });
fs.writeFileSync(
  evidencePath,
  [
    `date: ${new Date().toISOString()}`,
    `hook: .cursor/hooks/block-db-destruction.mjs`,
    `runner: .cursor/hooks/test-block-db-destruction.mjs`,
    "",
    ...lines,
    "",
    failed === 0 ? "ALL_CASES_PASSED" : `FAILED=${failed}`,
    "",
  ].join("\n"),
  "utf8"
);

if (failed > 0) {
  process.exit(1);
}

console.log("ALL_CASES_PASSED");
