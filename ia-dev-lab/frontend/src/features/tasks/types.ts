export type TaskPriority = "high" | "medium" | "low";

export type Task = {
  id: number;
  title: string;
  done: boolean;
  archived: boolean;
  due_date: string | null;
  priority: TaskPriority;
};

export const PRIORITY_LABELS: Record<TaskPriority, string> = {
  high: "Alta",
  medium: "Média",
  low: "Baixa",
};
