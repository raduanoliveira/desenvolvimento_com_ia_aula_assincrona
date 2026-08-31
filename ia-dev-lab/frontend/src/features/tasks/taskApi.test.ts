import { archiveTask, createTask, listTasks } from "./taskApi";

describe("taskApi", () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it("envia POST para criar tarefa com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ data: { id: 1, title: "Comprar pão", done: false } }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await createTask("Comprar pão");

    expect(task.title).toBe("Comprar pão");
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks"),
      expect.objectContaining({ method: "POST", credentials: "include" })
    );
  });

  it("envia PATCH para arquivar tarefa com credentials include", async () => {
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
      expect.objectContaining({ method: "PATCH", credentials: "include" })
    );
  });

  it("envia GET para listar tarefas com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ data: [] }),
    });
    vi.stubGlobal("fetch", fetchMock);

    await listTasks();

    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks"),
      expect.objectContaining({ credentials: "include" })
    );
  });
});
