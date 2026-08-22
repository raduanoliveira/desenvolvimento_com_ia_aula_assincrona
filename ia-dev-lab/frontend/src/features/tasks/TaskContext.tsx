import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { archiveTask as archiveTaskRequest, createTask, deleteTask, listTasks, toggleTask } from "./taskApi";
import type { Task } from "./types";

type TaskContextValue = {
  tasks: Task[];
  error: string | null;
  loadTasks: () => Promise<void>;
  addTask: (title: string) => Promise<void>;
  completeTask: (id: number) => Promise<void>;
  archiveTask: (id: number) => Promise<void>;
  removeTask: (id: number) => Promise<void>;
};

const TaskContext = createContext<TaskContextValue | null>(null);

export function TaskProvider({ children }: { children: ReactNode }) {
  const [tasks, setTasks] = useState<Task[]>([]);
  const [error, setError] = useState<string | null>(null);

  const loadTasks = useCallback(async () => {
    try {
      setError(null);
      setTasks(await listTasks());
    } catch {
      setError("Não foi possível carregar as tarefas.");
    }
  }, []);

  const addTask = useCallback(async (title: string) => {
    const created = await createTask(title);
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

  useEffect(() => {
    void loadTasks();
  }, [loadTasks]);

  const value = useMemo(
    () => ({ tasks, error, loadTasks, addTask, completeTask, archiveTask, removeTask }),
    [tasks, error, loadTasks, addTask, completeTask, archiveTask, removeTask]
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
