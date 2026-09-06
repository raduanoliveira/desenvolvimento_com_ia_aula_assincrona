import Alert from "@mui/material/Alert";
import Snackbar from "@mui/material/Snackbar";
import { useTaskContext } from "./TaskContext";

export function DueRemindersToaster() {
  const { reminders, dismissReminders } = useTaskContext();
  const items = reminders ?? [];

  const titles = items.map((task) => task.title).join(", ");
  const message =
    items.length === 1
      ? `Aviso: a tarefa "${titles}" vence amanhã.`
      : `Aviso: ${items.length} tarefas vencem amanhã: ${titles}.`;

  return (
    <Snackbar
      open={items.length > 0}
      anchorOrigin={{ vertical: "top", horizontal: "center" }}
      onClose={dismissReminders}
    >
      <Alert severity="warning" variant="filled" onClose={dismissReminders} sx={{ width: "100%" }}>
        {message}
      </Alert>
    </Snackbar>
  );
}
