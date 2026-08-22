import { archiveTask, createTask } from "./taskApi";

describe("taskApi", () => {
  it("envia POST para criar tarefa", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ data: { id: 1, title: "Comprar pão", done: false } }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await createTask("Comprar pão");

    expect(task.title).toBe("Comprar pão");
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks"),
      expect.objectContaining({ method: "POST" })
    );

    vi.unstubAllGlobals();
  });

  it("envia PATCH para arquivar tarefa", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: { id: 3, title: "Relatório", done: false, archived: true },
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await archiveTask(3);

    expect(task.archived).toBe(true);
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks/3/archive"),
      expect.objectContaining({ method: "PATCH" })
    );

    vi.unstubAllGlobals();
  });
});
