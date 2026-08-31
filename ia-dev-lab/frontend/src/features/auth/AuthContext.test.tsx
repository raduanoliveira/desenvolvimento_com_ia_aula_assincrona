import { fireEvent, render, screen } from "@testing-library/react";
import { AuthProvider, useAuthContext } from "./AuthContext";
import * as authApi from "./authApi";

vi.mock("./authApi", () => ({
  getSession: vi.fn(),
  endSession: vi.fn(),
  googleStartUrl: "http://localhost:8000/auth/google",
}));

function Probe() {
  const { status, user, error, signOut } = useAuthContext();
  return (
    <div>
      <p>status:{status}</p>
      <p>user:{user?.name ?? "none"}</p>
      <p>error:{error ?? "none"}</p>
      <button type="button" onClick={() => void signOut()}>
        sair-probe
      </button>
    </div>
  );
}

describe("AuthContext", () => {
  afterEach(() => {
    window.history.replaceState({}, "", "/");
  });

  it("fica signedOut quando getSession devolve null", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue(null);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>
    );

    expect(await screen.findByText("status:signedOut")).toBeInTheDocument();
    expect(screen.getByText("user:none")).toBeInTheDocument();
  });

  it("fica signedIn com o usuário de getSession", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue({
      id: 1,
      name: "Ana Silva",
      email: "ana@example.com",
    });

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>
    );

    expect(await screen.findByText("status:signedIn")).toBeInTheDocument();
    expect(screen.getByText("user:Ana Silva")).toBeInTheDocument();
  });

  it("signOut chama endSession e volta a signedOut", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue({
      id: 1,
      name: "Ana Silva",
      email: "ana@example.com",
    });
    vi.mocked(authApi.endSession).mockResolvedValue(undefined);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>
    );

    expect(await screen.findByText("status:signedIn")).toBeInTheDocument();
    fireEvent.click(screen.getByRole("button", { name: "sair-probe" }));

    expect(await screen.findByText("status:signedOut")).toBeInTheDocument();
    expect(authApi.endSession).toHaveBeenCalledTimes(1);
  });

  it("mostra mensagem clara quando a URL tem signin=cancelled", async () => {
    window.history.replaceState({}, "", "/?signin=cancelled");
    vi.mocked(authApi.getSession).mockResolvedValue(null);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>
    );

    expect(await screen.findByText("error:A entrada com Google não foi concluída.")).toBeInTheDocument();
    expect(screen.getByText("status:signedOut")).toBeInTheDocument();
  });

  it("mostra mensagem clara quando a URL tem signin=error", async () => {
    window.history.replaceState({}, "", "/?signin=error");
    vi.mocked(authApi.getSession).mockResolvedValue(null);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>
    );

    expect(await screen.findByText("error:Não foi possível entrar com o Google. Tente de novo.")).toBeInTheDocument();
    expect(screen.getByText("status:signedOut")).toBeInTheDocument();
  });
});
