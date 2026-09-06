import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import {
  archiveTask as archiveTaskRequest,
  createTask,
  deleteTask,
  listDueTomorrowReminders,
  listTasks,
  toggleTask,
  type CreateTaskInput,
  type TaskStatusFilterValue,
} from "./taskApi";
import type { Task } from "./types";

type TaskContextValue = {
  tasks: Task[];
  reminders: Task[];
  statusFilter: TaskStatusFilterValue;
  error: string | null;
  loadTasks: () => Promise<void>;
  setStatusFilter: (status: TaskStatusFilterValue) => void;
  addTask: (input: CreateTaskInput) => Promise<void>;
  completeTask: (id: number) => Promise<void>;
  archiveTask: (id: number) => Promise<void>;
  removeTask: (id: number) => Promise<void>;
  dismissReminders: () => void;
};

const TaskContext = createContext<TaskContextValue | null>(null);

export function TaskProvider({ children }: { children: ReactNode }) {
  const [tasks, setTasks] = useState<Task[]>([]);
  const [reminders, setReminders] = useState<Task[]>([]);
  const [statusFilter, setStatusFilterState] = useState<TaskStatusFilterValue>("all");
  const [error, setError] = useState<string | null>(null);

  const loadTasks = useCallback(async () => {
    try {
      setError(null);
      const [nextTasks, nextReminders] = await Promise.all([
        listTasks(statusFilter),
        listDueTomorrowReminders(),
      ]);
      setTasks(nextTasks);
      setReminders(nextReminders);
    } catch {
      setError("Não foi possível carregar as tarefas.");
    }
  }, [statusFilter]);

  const setStatusFilter = useCallback((status: TaskStatusFilterValue) => {
    setStatusFilterState(status);
  }, []);

  const addTask = useCallback(async (input: CreateTaskInput) => {
    const created = await createTask(input);
    setTasks((current) => [created, ...current]);
  }, []);

  const completeTask = useCallback(async (id: number) => {
    const updated = await toggleTask(id);
    setTasks((current) => current.map((task) => (task.id === id ? updated : task)));
  }, []);

  const archiveTask = useCallback(async (id: number) => {
    await archiveTaskRequest(id);
    setTasks((current) => current.filter((task) => task.id !== id));
  }, []);

  const removeTask = useCallback(async (id: number) => {
    await deleteTask(id);
    setTasks((current) => current.filter((task) => task.id !== id));
  }, []);

  const dismissReminders = useCallback(() => {
    setReminders([]);
  }, []);

  useEffect(() => {
    void loadTasks();
  }, [loadTasks]);

  const value = useMemo(
    () => ({
      tasks,
      reminders,
      statusFilter,
      error,
      loadTasks,
      setStatusFilter,
      addTask,
      completeTask,
      archiveTask,
      removeTask,
      dismissReminders,
    }),
    [
      tasks,
      reminders,
      statusFilter,
      error,
      loadTasks,
      setStatusFilter,
      addTask,
      completeTask,
      archiveTask,
      removeTask,
      dismissReminders,
    ]
  );

  return <TaskContext.Provider value={value}>{children}</TaskContext.Provider>;
}

export function useTaskContext(): TaskContextValue {
  const context = useContext(TaskContext);
  if (!context) {
    throw new Error("useTaskContext deve ser usado dentro de TaskProvider");
  }
  return context;
}
