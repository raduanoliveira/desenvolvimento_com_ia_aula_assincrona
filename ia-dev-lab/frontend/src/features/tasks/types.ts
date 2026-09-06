export type Task = {
  id: number;
  title: string;
  done: boolean;
  archived: boolean;
  due_date: string | null;
};
