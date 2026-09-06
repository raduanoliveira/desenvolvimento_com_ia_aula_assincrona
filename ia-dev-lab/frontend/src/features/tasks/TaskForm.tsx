import AddIcon from "@mui/icons-material/Add";
import { FormEvent, useState } from "react";
import Button from "@mui/material/Button";
import Stack from "@mui/material/Stack";
import TextField from "@mui/material/TextField";
import { useTaskContext } from "./TaskContext";

export function TaskForm() {
  const { addTask } = useTaskContext();
  const [title, setTitle] = useState("");
  const [dueDate, setDueDate] = useState("");

  async function onSubmit(event: FormEvent) {
    event.preventDefault();
    const value = title.trim();
    if (!value) {
      return;
    }
    await addTask({
      title: value,
      due_date: dueDate || null,
    });
    setTitle("");
    setDueDate("");
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
      <Button type="submit" variant="contained" startIcon={<AddIcon />} sx={{ px: 3, whiteSpace: "nowrap" }}>
        Adicionar
      </Button>
    </Stack>
  );
}
