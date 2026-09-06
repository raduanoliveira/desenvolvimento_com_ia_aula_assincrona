import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import CardHeader from "@mui/material/CardHeader";
import CircularProgress from "@mui/material/CircularProgress";
import Stack from "@mui/material/Stack";
import Box from "@mui/material/Box";
import { SignInScreen } from "./features/auth/SignInScreen";
import { useAuthContext } from "./features/auth/AuthContext";
import { DueRemindersToaster } from "./features/tasks/DueRemindersToaster";
import { TaskForm } from "./features/tasks/TaskForm";
import { TaskList } from "./features/tasks/TaskList";
import { TaskStatusFilter } from "./features/tasks/TaskStatusFilter";
import { TaskSummary } from "./features/tasks/TaskSummary";
import { TaskProvider, useTaskContext } from "./features/tasks/TaskContext";
import { DashboardLayout } from "./layout/DashboardLayout";

function TaskDashboard() {
  const { statusFilter, setStatusFilter } = useTaskContext();

  return (
    <DashboardLayout title="Minhas tarefas" subtitle="Crie, conclua, arquive ou exclua tarefas.">
      <DueRemindersToaster />
      <Stack spacing={3}>
        <TaskSummary />
        <Card sx={{ borderRadius: 4 }}>
          <CardHeader title="Nova tarefa" subheader="O título é obrigatório; o prazo é opcional" />
          <CardContent>
            <TaskForm />
          </CardContent>
        </Card>
        <Card sx={{ borderRadius: 4 }}>
          <CardHeader title="Lista" subheader="Marque para concluir, arquive ou exclua o item" />
          <CardContent>
            <Stack spacing={2}>
              <TaskStatusFilter value={statusFilter} onChange={setStatusFilter} />
              <TaskList />
            </Stack>
          </CardContent>
        </Card>
      </Stack>
    </DashboardLayout>
  );
}

export default function App() {
  const { status } = useAuthContext();

  if (status === "loading") {
    return (
      <Box sx={{ minHeight: "100vh", display: "flex", alignItems: "center", justifyContent: "center" }}>
        <CircularProgress aria-label="Carregando sessão" />
      </Box>
    );
  }

  if (status === "signedOut") {
    return <SignInScreen />;
  }

  return (
    <TaskProvider>
      <TaskDashboard />
    </TaskProvider>
  );
}
