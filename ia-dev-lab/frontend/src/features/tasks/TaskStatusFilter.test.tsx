import { fireEvent, render, screen } from "@testing-library/react";
import { TaskStatusFilter } from "./TaskStatusFilter";

describe("TaskStatusFilter", () => {
  it("mostra as opções Todas, Pendentes e Concluídas e notifica a mudança", () => {
    const onChange = vi.fn();

    render(<TaskStatusFilter value="all" onChange={onChange} />);

    expect(screen.getByRole("button", { name: "Todas" })).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "Pendentes" })).toBeInTheDocument();
    expect(screen.getByRole("button", { name: "Concluídas" })).toBeInTheDocument();

    fireEvent.click(screen.getByRole("button", { name: "Pendentes" }));

    expect(onChange).toHaveBeenCalledWith("pending");
  });
});
