import { render, screen } from "@testing-library/react";
import { SignInScreen } from "./SignInScreen";
import { useAuthContext } from "./AuthContext";

vi.mock("./AuthContext", () => ({
  useAuthContext: vi.fn(),
}));

describe("SignInScreen", () => {
  it("mostra Continuar com Google e não mostra a lista de tarefas", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      user: null,
      status: "signedOut",
      error: null,
      googleStartUrl: "http://localhost:8000/auth/google",
      signOut: vi.fn(),
    });

    render(<SignInScreen />);

    const link = screen.getByRole("link", { name: /continuar com google/i });
    expect(link).toHaveAttribute("href", "http://localhost:8000/auth/google");
    expect(screen.queryByText("Nova tarefa")).not.toBeInTheDocument();
    expect(screen.queryByText("Lista")).not.toBeInTheDocument();
  });

  it("mostra alerta de cancelamento e mantém Continuar com Google", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      user: null,
      status: "signedOut",
      error: "A entrada com Google não foi concluída.",
      googleStartUrl: "http://localhost:8000/auth/google",
      signOut: vi.fn(),
    });

    render(<SignInScreen />);

    expect(screen.getByRole("alert")).toHaveTextContent("A entrada com Google não foi concluída.");
    expect(screen.getByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
    expect(screen.queryByText("Nova tarefa")).not.toBeInTheDocument();
  });

  it("mostra alerta de erro do Google e mantém Continuar com Google", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      user: null,
      status: "signedOut",
      error: "Não foi possível entrar com o Google. Tente de novo.",
      googleStartUrl: "http://localhost:8000/auth/google",
      signOut: vi.fn(),
    });

    render(<SignInScreen />);

    expect(screen.getByRole("alert")).toHaveTextContent("Não foi possível entrar com o Google. Tente de novo.");
    expect(screen.getByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
  });
});
