import type { SessionUser } from "./types";

const apiUrl = import.meta.env.VITE_API_URL ?? "http://localhost:8000/api";

export const googleStartUrl = `${apiUrl.replace(/\/api\/?$/, "")}/auth/google`;
export const githubStartUrl = `${apiUrl.replace(/\/api\/?$/, "")}/auth/github`;

async function parseError(response: Response): Promise<Error> {
  return new Error(`HTTP ${response.status}`);
}

export async function getSession(): Promise<SessionUser | null> {
  const response = await fetch(`${apiUrl}/session`, {
    credentials: "include",
    headers: { Accept: "application/json" },
  });

  if (response.status === 401) {
    return null;
  }

  if (!response.ok) {
    throw await parseError(response);
  }

  const payload = await response.json();
  return payload.data ?? payload;
}

export async function endSession(): Promise<void> {
  const response = await fetch(`${apiUrl}/session`, {
    method: "DELETE",
    credentials: "include",
    headers: { Accept: "application/json" },
  });

  if (!response.ok && response.status !== 204) {
    throw await parseError(response);
  }
}
