import { fireEvent, render, screen } from "@testing-library/react";
import { TaskProvider, useTaskContext } from "./TaskContext";
import * as api from "./taskApi";

vi.mock("./taskApi");

function TasksProbe() {
  const { tasks, error, archiveTask } = useTaskContext();
  if (error) {
    return <p>{error}</p>;
  }
  return (
    <div>
      <p>{tasks.map((task) => task.title).join(", ") || "vazio"}</p>
      {tasks.map((task) => (
        <button key={task.id} type="button" onClick={() => void archiveTask(task.id)}>
          arquivar {task.title}
        </button>
      ))}
    </div>
  );
}

describe("TaskContext", () => {
  it("carrega tarefas pela API e expõe no contexto", async () => {
    vi.mocked(api.listTasks).mockResolvedValue([
      { id: 1, title: "Estudar SOLID", done: false, archived: false },
    ]);

    render(
      <TaskProvider>
        <TasksProbe />
      </TaskProvider>
    );

    expect(await screen.findByText("Estudar SOLID")).toBeInTheDocument();
  });

  it("remove a tarefa arquivada da lista ativa exposta pelo contexto", async () => {
    vi.mocked(api.listTasks).mockResolvedValue([
      { id: 1, title: "Estudar SOLID", done: false, archived: false },
      { id: 2, title: "Relatório", done: false, archived: false },
    ]);
    vi.mocked(api.archiveTask).mockResolvedValue({
      id: 1,
      title: "Estudar SOLID",
      done: false,
      archived: true,
    });

    render(
      <TaskProvider>
        <TasksProbe />
      </TaskProvider>
    );

    expect(await screen.findByText("Estudar SOLID, Relatório")).toBeInTheDocument();
    fireEvent.click(screen.getByRole("button", { name: "arquivar Estudar SOLID" }));

    expect(await screen.findByText("Relatório")).toBeInTheDocument();
    expect(screen.queryByText("Estudar SOLID, Relatório")).not.toBeInTheDocument();
    expect(api.archiveTask).toHaveBeenCalledWith(1);
  });
});
