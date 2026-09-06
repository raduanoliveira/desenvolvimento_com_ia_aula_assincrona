import type { Task } from "./types";

const apiUrl = import.meta.env.VITE_API_URL ?? "http://localhost:8000/api";

const jsonHeaders = { Accept: "application/json" };

async function parseError(response: Response): Promise<Error> {
  return new Error(`HTTP ${response.status}`);
}

export type TaskStatusFilterValue = "all" | "pending" | "done";

export async function listTasks(status: TaskStatusFilterValue = "all"): Promise<Task[]> {
  const query = status === "all" ? "" : `?status=${encodeURIComponent(status)}`;
  const response = await fetch(`${apiUrl}/tasks${query}`, {
    credentials: "include",
    headers: jsonHeaders,
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export type CreateTaskInput = {
  title: string;
  due_date?: string | null;
};

export async function createTask(input: CreateTaskInput): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks`, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json", ...jsonHeaders },
    body: JSON.stringify({
      title: input.title,
      due_date: input.due_date || null,
    }),
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function listDueTomorrowReminders(): Promise<Task[]> {
  const response = await fetch(`${apiUrl}/reminders/due-tomorrow`, {
    credentials: "include",
    headers: jsonHeaders,
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function updateTaskTitle(id: number, title: string): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks/${id}`, {
    method: "PATCH",
    credentials: "include",
    headers: { "Content-Type": "application/json", ...jsonHeaders },
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
    credentials: "include",
    headers: jsonHeaders,
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}

export async function deleteTask(id: number): Promise<void> {
  const response = await fetch(`${apiUrl}/tasks/${id}`, {
    method: "DELETE",
    credentials: "include",
    headers: jsonHeaders,
  });
  if (!response.ok && response.status !== 204) {
    throw await parseError(response);
  }
}

export async function archiveTask(id: number): Promise<Task> {
  const response = await fetch(`${apiUrl}/tasks/${id}/archive`, {
    method: "PATCH",
    credentials: "include",
    headers: jsonHeaders,
  });
  if (!response.ok) {
    throw await parseError(response);
  }
  const payload = await response.json();
  return payload.data ?? payload;
}
