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
      error: null,
      loadTasks: vi.fn(),
      addTask,
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
    });
    expect(await screen.findByLabelText("Nova tarefa")).toHaveValue("");
    expect(screen.getByLabelText("Prazo")).toHaveValue("");
  });
});
