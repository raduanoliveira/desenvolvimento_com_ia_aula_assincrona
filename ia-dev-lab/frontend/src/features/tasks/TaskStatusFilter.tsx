import ToggleButton from "@mui/material/ToggleButton";
import ToggleButtonGroup from "@mui/material/ToggleButtonGroup";
import type { TaskStatusFilterValue } from "./taskApi";

type TaskStatusFilterProps = {
  value: TaskStatusFilterValue;
  onChange: (value: TaskStatusFilterValue) => void;
};

export function TaskStatusFilter({ value, onChange }: TaskStatusFilterProps) {
  return (
    <ToggleButtonGroup
      exclusive
      size="small"
      value={value}
      onChange={(_event, next) => {
        if (next !== null) {
          onChange(next);
        }
      }}
      aria-label="Filtrar tarefas por status"
    >
      <ToggleButton value="all">Todas</ToggleButton>
      <ToggleButton value="pending">Pendentes</ToggleButton>
      <ToggleButton value="done">Concluídas</ToggleButton>
    </ToggleButtonGroup>
  );
}
