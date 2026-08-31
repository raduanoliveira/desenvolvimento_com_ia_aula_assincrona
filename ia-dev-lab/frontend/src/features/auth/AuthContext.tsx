import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { endSession, getSession, githubStartUrl, googleStartUrl } from "./authApi";
import type { SessionUser } from "./types";

export type AuthStatus = "loading" | "signedOut" | "signedIn";

export type AuthContextValue = {
  user: SessionUser | null;
  status: AuthStatus;
  error: string | null;
  googleStartUrl: string;
  githubStartUrl: string;
  signOut: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

function messageFromSignInQuery(search: string): string | null {
  const signin = new URLSearchParams(search).get("signin");
  if (signin === "cancelled") {
    return "A entrada não foi concluída.";
  }
  if (signin === "error") {
    return "Não foi possível entrar. Tente de novo.";
  }
  return null;
}

function clearSignInQuery(): void {
  const url = new URL(window.location.href);
  if (!url.searchParams.has("signin")) {
    return;
  }
  url.searchParams.delete("signin");
  const next = `${url.pathname}${url.search}${url.hash}`;
  window.history.replaceState({}, "", next);
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<SessionUser | null>(null);
  const [status, setStatus] = useState<AuthStatus>("loading");
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fromQuery = messageFromSignInQuery(window.location.search);
    if (fromQuery) {
      setError(fromQuery);
      clearSignInQuery();
    }

    void getSession()
      .then((session) => {
        setUser(session);
        setStatus(session ? "signedIn" : "signedOut");
      })
      .catch(() => {
        setUser(null);
        setStatus("signedOut");
        setError((current) => current ?? "Não foi possível entrar. Tente de novo.");
      });
  }, []);

  const signOut = useCallback(async () => {
    await endSession();
    setUser(null);
    setStatus("signedOut");
    setError(null);
  }, []);

  const value = useMemo(
    () => ({ user, status, error, googleStartUrl, githubStartUrl, signOut }),
    [user, status, error, signOut]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuthContext(): AuthContextValue {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuthContext deve ser usado dentro de AuthProvider");
  }
  return context;
}
