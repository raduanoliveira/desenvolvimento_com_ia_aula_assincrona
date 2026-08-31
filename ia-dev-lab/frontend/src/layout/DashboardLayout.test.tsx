import { fireEvent, render, screen } from "@testing-library/react";
import { DashboardLayout } from "./DashboardLayout";
import { useAuthContext } from "../features/auth/AuthContext";

vi.mock("../features/auth/AuthContext", () => ({
  useAuthContext: vi.fn(),
}));

describe("DashboardLayout", () => {
  it("mostra o nome da sessão e chama signOut ao clicar em Sair", () => {
    const signOut = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useAuthContext).mockReturnValue({
      user: { id: 1, name: "Ana Silva", email: "ana@example.com" },
      status: "signedIn",
      error: null,
      googleStartUrl: "http://localhost:8000/auth/google",
      githubStartUrl: "http://localhost:8000/auth/github",
      signOut,
    });

    render(
      <DashboardLayout title="Minhas tarefas">
        <p>conteúdo</p>
      </DashboardLayout>
    );

    expect(screen.getByText("Ana Silva")).toBeInTheDocument();
    fireEvent.click(screen.getByRole("button", { name: "Sair" }));
    expect(signOut).toHaveBeenCalledTimes(1);
  });
});
