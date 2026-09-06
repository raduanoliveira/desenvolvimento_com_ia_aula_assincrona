import { fireEvent, render, screen } from "@testing-library/react";
import { TaskForm } from "./TaskForm";
import { useTaskContext } from "./TaskContext";

vi.mock("./TaskContext", () => ({
  useTaskContext: vi.fn(),
}));

describe("TaskForm", () => {
  it("envia o título e o prazo para addTask e limpa os campos", async () => {
    const addTask = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [],
      reminders: [],
      statusFilter: "all",
      error: null,
      loadTasks: vi.fn(),
      setStatusFilter: vi.fn(),
      addTask,
      renameTask: vi.fn(),
      changePriority: vi.fn(),
      completeTask: vi.fn(),
      archiveTask: vi.fn(),
      removeTask: vi.fn(),
      dismissReminders: vi.fn(),
    });

    render(<TaskForm />);

    fireEvent.change(screen.getByLabelText("Nova tarefa"), {
      target: { value: "Comprar pão" },
    });
    fireEvent.change(screen.getByLabelText("Prazo"), {
      target: { value: "2026-09-10" },
    });
    fireEvent.click(screen.getByRole("button", { name: "Adicionar" }));

    expect(addTask).toHaveBeenCalledWith({
      title: "Comprar pão",
      due_date: "2026-09-10",
      priority: "medium",
    });
    expect(await screen.findByLabelText("Nova tarefa")).toHaveValue("");
    expect(screen.getByLabelText("Prazo")).toHaveValue("");
  });

  it("envia a prioridade selecionada para addTask", async () => {
    const addTask = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [],
      reminders: [],
      statusFilter: "all",
      error: null,
      loadTasks: vi.fn(),
      setStatusFilter: vi.fn(),
      addTask,
      renameTask: vi.fn(),
      changePriority: vi.fn(),
      completeTask: vi.fn(),
      archiveTask: vi.fn(),
      removeTask: vi.fn(),
      dismissReminders: vi.fn(),
    });

    render(<TaskForm />);

    fireEvent.change(screen.getByLabelText("Nova tarefa"), {
      target: { value: "Entrega urgente" },
    });
    await screen.findByLabelText("Prioridade");
    fireEvent.mouseDown(screen.getByLabelText("Prioridade"));
    fireEvent.click(await screen.findByRole("option", { name: "Alta" }));
    await screen.findByRole("button", { name: "Adicionar" });
    fireEvent.click(screen.getByRole("button", { name: "Adicionar" }));

    await screen.findByLabelText("Nova tarefa");

    expect(addTask).toHaveBeenCalledWith({
      title: "Entrega urgente",
      due_date: null,
      priority: "high",
    });
  });
});
