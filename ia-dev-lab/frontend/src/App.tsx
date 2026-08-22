import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import CardHeader from "@mui/material/CardHeader";
import Stack from "@mui/material/Stack";
import { TaskForm } from "./features/tasks/TaskForm";
import { TaskList } from "./features/tasks/TaskList";
import { TaskSummary } from "./features/tasks/TaskSummary";
import { DashboardLayout } from "./layout/DashboardLayout";

export default function App() {
  return (
    <DashboardLayout title="Minhas tarefas" subtitle="Crie, conclua, arquive ou exclua tarefas.">
      <Stack spacing={3}>
        <TaskSummary />
        <Card sx={{ borderRadius: 4 }}>
          <CardHeader title="Nova tarefa" subheader="O título é obrigatório" />
          <CardContent>
            <TaskForm />
          </CardContent>
        </Card>
        <Card sx={{ borderRadius: 4 }}>
          <CardHeader title="Lista" subheader="Marque para concluir, arquive ou exclua o item" />
          <CardContent>
            <TaskList />
          </CardContent>
        </Card>
      </Stack>
    </DashboardLayout>
  );
}
