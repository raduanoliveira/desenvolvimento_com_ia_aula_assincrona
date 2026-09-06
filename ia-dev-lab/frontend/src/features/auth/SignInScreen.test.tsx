import { render, screen } from "@testing-library/react";
import { SignInScreen } from "./SignInScreen";
import { useAuthContext } from "./AuthContext";

vi.mock("./AuthContext", () => ({
  useAuthContext: vi.fn(),
}));

const signedOut = {
  user: null,
  status: "signedOut" as const,
  googleStartUrl: "http://localhost:8000/auth/google",
  githubStartUrl: "http://localhost:8000/auth/github",
  signOut: vi.fn(),
};

describe("SignInScreen", () => {
  it("mostra Continuar com Google e Continuar com GitHub e não mostra a lista", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      ...signedOut,
      error: null,
    });

    render(<SignInScreen />);

    const google = screen.getByRole("link", { name: /continuar com google/i });
    const github = screen.getByRole("link", { name: /continuar com github/i });
    expect(google).toHaveAttribute("href", "http://localhost:8000/auth/google");
    expect(github).toHaveAttribute("href", "http://localhost:8000/auth/github");
    expect(screen.queryByText("Nova tarefa")).not.toBeInTheDocument();
    expect(screen.queryByText("Lista")).not.toBeInTheDocument();
  });

  it("mostra alerta de cancelamento e mantém Google e GitHub", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      ...signedOut,
      error: "A entrada não foi concluída.",
    });

    render(<SignInScreen />);

    expect(screen.getByRole("alert")).toHaveTextContent("A entrada não foi concluída.");
    expect(screen.getByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
    expect(screen.getByRole("link", { name: /continuar com github/i })).toBeInTheDocument();
    expect(screen.queryByText("Nova tarefa")).not.toBeInTheDocument();
  });

  it("mostra alerta de erro e mantém Google e GitHub", () => {
    vi.mocked(useAuthContext).mockReturnValue({
      ...signedOut,
      error: "Não foi possível entrar. Tente de novo.",
    });

    render(<SignInScreen />);

    expect(screen.getByRole("alert")).toHaveTextContent("Não foi possível entrar. Tente de novo.");
    expect(screen.getByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
    expect(screen.getByRole("link", { name: /continuar com github/i })).toBeInTheDocument();
  });
});
