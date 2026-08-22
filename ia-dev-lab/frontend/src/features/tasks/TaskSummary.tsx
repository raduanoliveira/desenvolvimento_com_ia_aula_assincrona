import AssignmentIcon from "@mui/icons-material/Assignment";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import PendingActionsIcon from "@mui/icons-material/PendingActions";
import Avatar from "@mui/material/Avatar";
import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import Stack from "@mui/material/Stack";
import Typography from "@mui/material/Typography";
import { useTaskContext } from "./TaskContext";

export function TaskSummary() {
  const { tasks } = useTaskContext();
  const done = tasks.filter((task) => task.done).length;
  const pending = tasks.length - done;

  const cards = [
    {
      label: "Total",
      value: tasks.length,
      icon: <AssignmentIcon />,
      bgcolor: "primary.light",
      color: "primary.dark",
    },
    {
      label: "Pendentes",
      value: pending,
      icon: <PendingActionsIcon />,
      bgcolor: "warning.light",
      color: "warning.dark",
    },
    {
      label: "Concluídas",
      value: done,
      icon: <CheckCircleIcon />,
      bgcolor: "success.light",
      color: "success.dark",
    },
  ];

  return (
    <Stack direction={{ xs: "column", sm: "row" }} spacing={2}>
      {cards.map((card) => (
        <Card key={card.label} sx={{ flex: 1, borderRadius: 4 }}>
          <CardContent>
            <Stack direction="row" alignItems="center" justifyContent="space-between">
              <div>
                <Typography variant="body2" color="text.secondary">
                  {card.label}
                </Typography>
                <Typography variant="h4" fontWeight={700}>
                  {card.value}
                </Typography>
              </div>
              <Avatar sx={{ bgcolor: card.bgcolor, color: card.color }}>{card.icon}</Avatar>
            </Stack>
          </CardContent>
        </Card>
      ))}
    </Stack>
  );
}
