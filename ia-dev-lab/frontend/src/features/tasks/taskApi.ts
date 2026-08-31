import type { Task } from "./types";

const apiUrl = import.meta.env.VITE_API_URL ?? "http://localhost:8000/api";

async function parseError(response: Response): Promise<Error> {
  return new Error(`HTTP ${response.status}`);
}

export async function listTasks(): Promise<Task[]> {
  const response = await fetch(`${apiUrl}/tasks`);
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function createTask(title: string): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks`, {
    method: "POST",
    headers: { "Content-Type": "application/json", Accept: "application/json" },
    body: JSON.stringify({ title }),
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function toggleTask(id: number): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks/${id}/toggle`, {
    method: "PATCH",
    headers: { Accept: "application/json" },
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function deleteTask(id: number): Promise<void> {
  const response = await fetch(`${apiUrl}/tasks/${id}`, { method: "DELETE" });
  if (!response.ok && response.status !== 204) {
    throw await parseError(response);
  }
}

export async function archiveTask(id: number): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks/${id}/archive`, {
    method: "PATCH",
    headers: { Accept: "application/json" },
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}
