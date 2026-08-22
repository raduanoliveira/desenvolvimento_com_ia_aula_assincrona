import { fireEvent, render, screen } from "@testing-library/react";
import { TaskList } from "./TaskList";
import { useTaskContext } from "./TaskContext";

vi.mock("./TaskContext", () => ({
  useTaskContext: vi.fn(),
}));

describe("TaskList", () => {
  it("mostra tarefas e chama concluir, arquivar e excluir", () => {
    const completeTask = vi.fn();
    const archiveTask = vi.fn();
    const removeTask = vi.fn();
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [{ id: 1, title: "Estudar MUI", done: false, archived: false }],
      error: null,
      loadTasks: vi.fn(),
      addTask: vi.fn(),
      completeTask,
      archiveTask,
      removeTask,
    });

    render(<TaskList />);

    expect(screen.getByText("Estudar MUI")).toBeInTheDocument();
    fireEvent.click(screen.getByLabelText("Concluir Estudar MUI"));
    fireEvent.click(screen.getByLabelText("Arquivar Estudar MUI"));
    fireEvent.click(screen.getByLabelText("Excluir Estudar MUI"));

    expect(completeTask).toHaveBeenCalledWith(1);
    expect(archiveTask).toHaveBeenCalledWith(1);
    expect(removeTask).toHaveBeenCalledWith(1);
  });
});
