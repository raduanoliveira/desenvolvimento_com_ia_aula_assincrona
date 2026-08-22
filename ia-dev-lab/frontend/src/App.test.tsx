import { render, screen } from "@testing-library/react";
import App from "./App";
import { TaskProvider } from "./features/tasks/TaskContext";
import * as api from "./features/tasks/taskApi";

vi.mock("./features/tasks/taskApi");

describe("App (layout Dashboard)", () => {
  it("mostra AppBar e o item Tarefas do Drawer", async () => {
    vi.mocked(api.listTasks).mockResolvedValue([]);

    render(
      <TaskProvider>
        <App />
      </TaskProvider>
    );

    expect(await screen.findByRole("banner")).toHaveTextContent("ToDo list");
    expect(screen.getAllByText("Tarefas").length).toBeGreaterThan(0);
    expect(screen.getByText("Minhas tarefas")).toBeInTheDocument();
    expect(screen.getByText("Pendentes")).toBeInTheDocument();
    expect(screen.getByText("Concluídas")).toBeInTheDocument();
  });
});
