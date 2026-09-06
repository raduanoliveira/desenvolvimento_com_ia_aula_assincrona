import AddIcon from "@mui/icons-material/Add";
import { FormEvent, useState } from "react";
import Button from "@mui/material/Button";
import FormControl from "@mui/material/FormControl";
import InputLabel from "@mui/material/InputLabel";
import MenuItem from "@mui/material/MenuItem";
import Select from "@mui/material/Select";
import Stack from "@mui/material/Stack";
import TextField from "@mui/material/TextField";
import { useTaskContext } from "./TaskContext";
import { PRIORITY_LABELS, type TaskPriority } from "./types";

export function TaskForm() {
  const { addTask } = useTaskContext();
  const [title, setTitle] = useState("");
  const [dueDate, setDueDate] = useState("");
  const [priority, setPriority] = useState<TaskPriority>("medium");

  async function onSubmit(event: FormEvent) {
    event.preventDefault();
    const value = title.trim();
    if (!value) {
      return;
    }
    await addTask({
      title: value,
      due_date: dueDate || null,
      priority,
    });
    setTitle("");
    setDueDate("");
    setPriority("medium");
  }

  return (
    <Stack component="form" direction={{ xs: "column", sm: "row" }} spacing={1.5} onSubmit={onSubmit}>
      <TextField
        label="Nova tarefa"
        placeholder="O que precisa ser feito?"
        value={title}
        onChange={(event) => setTitle(event.target.value)}
        fullWidth
      />
      <TextField
        label="Prazo"
        type="date"
        value={dueDate}
        onChange={(event) => setDueDate(event.target.value)}
        InputLabelProps={{ shrink: true }}
        sx={{ minWidth: { sm: 180 } }}
      />
      <FormControl sx={{ minWidth: { sm: 140 } }}>
        <InputLabel id="task-priority-label">Prioridade</InputLabel>
        <Select
          labelId="task-priority-label"
          label="Prioridade"
          value={priority}
          onChange={(event) => setPriority(event.target.value as TaskPriority)}
        >
          {(Object.keys(PRIORITY_LABELS) as TaskPriority[]).map((value) => (
            <MenuItem key={value} value={value}>
              {PRIORITY_LABELS[value]}
            </MenuItem>
          ))}
        </Select>
      </FormControl>
      <Button type="submit" variant="contained" startIcon={<AddIcon />} sx={{ px: 3, whiteSpace: "nowrap" }}>
        Adicionar
      </Button>
    </Stack>
  );
}
