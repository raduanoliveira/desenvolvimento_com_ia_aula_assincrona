import ArchiveOutlinedIcon from "@mui/icons-material/ArchiveOutlined";
import DeleteOutlineIcon from "@mui/icons-material/DeleteOutline";
import EditOutlinedIcon from "@mui/icons-material/EditOutlined";
import Alert from "@mui/material/Alert";
import Button from "@mui/material/Button";
import Checkbox from "@mui/material/Checkbox";
import Chip from "@mui/material/Chip";
import IconButton from "@mui/material/IconButton";
import List from "@mui/material/List";
import ListItem from "@mui/material/ListItem";
import ListItemIcon from "@mui/material/ListItemIcon";
import ListItemText from "@mui/material/ListItemText";
import Stack from "@mui/material/Stack";
import TextField from "@mui/material/TextField";
import Tooltip from "@mui/material/Tooltip";
import Typography from "@mui/material/Typography";
import { useState } from "react";
import { useTaskContext } from "./TaskContext";

function formatDueDate(dueDate: string): string {
  const [year, month, day] = dueDate.split("-");
  return `${day}/${month}/${year}`;
}

export function TaskList() {
  const { tasks, error, renameTask, completeTask, archiveTask, removeTask } = useTaskContext();
  const [editingId, setEditingId] = useState<number | null>(null);
  const [draftTitle, setDraftTitle] = useState("");

  if (error) {
    return <Alert severity="error">{error}</Alert>;
  }

  if (tasks.length === 0) {
    return (
      <Alert severity="info">
        <Typography variant="body2">Nenhuma tarefa ainda. Adicione a primeira acima.</Typography>
      </Alert>
    );
  }

  async function saveTitle(id: number) {
    const value = draftTitle.trim();
    if (!value) {
      return;
    }
    await renameTask(id, value);
    setEditingId(null);
    setDraftTitle("");
  }

  return (
    <List disablePadding>
      {tasks.map((task) => (
        <ListItem
          key={task.id}
          sx={{
            mb: 1.5,
            px: 1,
            borderRadius: 3,
            bgcolor: "background.paper",
            border: "1px solid",
            borderColor: "divider",
            "&:last-of-type": { mb: 0 },
          }}
          secondaryAction={
            <Stack direction="row" spacing={0.5}>
              <Tooltip title="Editar título">
                <IconButton
                  edge="end"
                  aria-label={`Editar ${task.title}`}
                  onClick={() => {
                    setEditingId(task.id);
                    setDraftTitle(task.title);
                  }}
                >
                  <EditOutlinedIcon />
                </IconButton>
              </Tooltip>
              <Tooltip title="Arquivar">
                <IconButton
                  edge="end"
                  aria-label={`Arquivar ${task.title}`}
                  onClick={() => void archiveTask(task.id)}
                >
                  <ArchiveOutlinedIcon />
                </IconButton>
              </Tooltip>
              <Tooltip title="Excluir">
                <IconButton edge="end" aria-label={`Excluir ${task.title}`} onClick={() => void removeTask(task.id)}>
                  <DeleteOutlineIcon />
                </IconButton>
              </Tooltip>
            </Stack>
          }
        >
          <ListItemIcon sx={{ minWidth: 42 }}>
            <Checkbox
              edge="start"
              checked={task.done}
              onChange={() => void completeTask(task.id)}
              inputProps={{ "aria-label": `Concluir ${task.title}` }}
            />
          </ListItemIcon>
          {editingId === task.id ? (
            <Stack direction={{ xs: "column", sm: "row" }} spacing={1} sx={{ width: "100%", pr: 8 }}>
              <TextField
                label="Título"
                value={draftTitle}
                onChange={(event) => setDraftTitle(event.target.value)}
                fullWidth
                size="small"
              />
              <Button variant="contained" onClick={() => void saveTitle(task.id)}>
                Salvar
              </Button>
            </Stack>
          ) : (
            <ListItemText
              primary={task.title}
              secondary={
                <Stack direction="row" spacing={1} useFlexGap flexWrap="wrap" sx={{ mt: 0.5 }}>
                  <Chip
                    size="small"
                    label={task.done ? "Concluída" : "Pendente"}
                    color={task.done ? "success" : "warning"}
                    variant="outlined"
                  />
                  {task.due_date ? (
                    <Chip size="small" label={`Prazo ${formatDueDate(task.due_date)}`} variant="outlined" />
                  ) : null}
                </Stack>
              }
              secondaryTypographyProps={{ component: "div" }}
              sx={{
                pr: 1,
                "& .MuiListItemText-primary": {
                  textDecoration: task.done ? "line-through" : "none",
                  color: task.done ? "text.secondary" : "text.primary",
                },
              }}
            />
          )}
        </ListItem>
      ))}
    </List>
  );
}
