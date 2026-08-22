import { fireEvent, render, screen } from "@testing-library/react";
import { TaskForm } from "./TaskForm";
import { useTaskContext } from "./TaskContext";

vi.mock("./TaskContext", () => ({
  useTaskContext: vi.fn(),
}));

describe("TaskForm", () => {
  it("envia o título para addTask e limpa o campo", async () => {
    const addTask = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [],
      error: null,
      loadTasks: vi.fn(),
      addTask,
      completeTask: vi.fn(),
      archiveTask: vi.fn(),
      removeTask: vi.fn(),
    });

    render(<TaskForm />);

    fireEvent.change(screen.getByLabelText("Nova tarefa"), {
      target: { value: "Comprar pão" },
    });
    fireEvent.click(screen.getByRole("button", { name: "Adicionar" }));

    expect(addTask).toHaveBeenCalledWith("Comprar pão");
    expect(await screen.findByLabelText("Nova tarefa")).toHaveValue("");
  });
});
