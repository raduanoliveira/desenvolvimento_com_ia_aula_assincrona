import { fireEvent, render, screen } from "@testing-library/react";
import { DueRemindersToaster } from "./DueRemindersToaster";
import { useTaskContext } from "./TaskContext";

vi.mock("./TaskContext", () => ({
  useTaskContext: vi.fn(),
}));

describe("DueRemindersToaster", () => {
  it("mostra o aviso quando há lembrete de prazo amanhã", () => {
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [],
      reminders: [{ id: 1, title: "Prova", done: false, archived: false, due_date: "2026-09-07", priority: "medium" }],
      statusFilter: "all",
      error: null,
      loadTasks: vi.fn(),
      setStatusFilter: vi.fn(),
      addTask: vi.fn(),
      renameTask: vi.fn(),
      changePriority: vi.fn(),
      completeTask: vi.fn(),
      archiveTask: vi.fn(),
      removeTask: vi.fn(),
      dismissReminders: vi.fn(),
    });

    render(<DueRemindersToaster />);

    expect(screen.getByText('Aviso: a tarefa "Prova" vence amanhã.')).toBeInTheDocument();
  });

  it("chama dismissReminders ao fechar o aviso", () => {
    const dismissReminders = vi.fn();
    vi.mocked(useTaskContext).mockReturnValue({
      tasks: [],
      reminders: [{ id: 1, title: "Prova", done: false, archived: false, due_date: "2026-09-07", priority: "medium" }],
      statusFilter: "all",
      error: null,
      loadTasks: vi.fn(),
      setStatusFilter: vi.fn(),
      addTask: vi.fn(),
      renameTask: vi.fn(),
      changePriority: vi.fn(),
      completeTask: vi.fn(),
      archiveTask: vi.fn(),
      removeTask: vi.fn(),
      dismissReminders,
    });

    render(<DueRemindersToaster />);
    fireEvent.click(screen.getByRole("button", { name: /close/i }));

    expect(dismissReminders).toHaveBeenCalled();
  });
});
