import { fireEvent, render, screen } from "@testing-library/react";
import App from "./App";
import { AuthProvider } from "./features/auth/AuthContext";
import * as authApi from "./features/auth/authApi";
import * as api from "./features/tasks/taskApi";

vi.mock("./features/auth/authApi", () => ({
  getSession: vi.fn(),
  endSession: vi.fn(),
  googleStartUrl: "http://localhost:8000/auth/google",
}));

vi.mock("./features/tasks/taskApi");

function renderApp() {
  return render(
    <AuthProvider>
      <App />
    </AuthProvider>
  );
}

describe("App", () => {
  it("mostra só a tela de entrada quando não há sessão", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue(null);

    renderApp();

    expect(await screen.findByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
    expect(screen.queryByText("Nova tarefa")).not.toBeInTheDocument();
    expect(screen.queryByText("Minhas tarefas")).not.toBeInTheDocument();
    expect(api.listTasks).not.toHaveBeenCalled();
  });

  it("mostra o dashboard da lista quando há sessão", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue({
      id: 1,
      name: "Ana Silva",
      email: "ana@example.com",
    });
    vi.mocked(api.listTasks).mockResolvedValue([
      { id: 1, title: "Pagar conta", done: false, archived: false },
    ]);

    renderApp();

    expect(await screen.findByText("Minhas tarefas")).toBeInTheDocument();
    expect(await screen.findByText("Pagar conta")).toBeInTheDocument();
    expect(screen.getByText("Ana Silva")).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "Sair" })).toBeInTheDocument();
    expect(screen.queryByRole("link", { name: /continuar com google/i })).not.toBeInTheDocument();
  });

  it("volta à tela de entrada depois de Sair", async () => {
    vi.mocked(authApi.getSession).mockResolvedValue({
      id: 1,
      name: "Ana Silva",
      email: "ana@example.com",
    });
    vi.mocked(authApi.endSession).mockResolvedValue(undefined);
    vi.mocked(api.listTasks).mockResolvedValue([]);

    renderApp();

    fireEvent.click(await screen.findByRole("button", { name: "Sair" }));

    expect(await screen.findByRole("link", { name: /continuar com google/i })).toBeInTheDocument();
    expect(screen.queryByText("Minhas tarefas")).not.toBeInTheDocument();
    expect(authApi.endSession).toHaveBeenCalledTimes(1);
  });
});
