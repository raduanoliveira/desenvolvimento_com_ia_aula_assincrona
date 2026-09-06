import { archiveTask, createTask, listDueTomorrowReminders, listTasks, updateTaskPriority } from "./taskApi";

describe("taskApi", () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it("envia POST para criar tarefa com prazo e credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: { id: 1, title: "Comprar pão", done: false, archived: false, due_date: "2026-09-10" },
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await createTask({ title: "Comprar pão", due_date: "2026-09-10" });

    expect(task.title).toBe("Comprar pão");
    expect(task.due_date).toBe("2026-09-10");
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks"),
      expect.objectContaining({ method: "POST", credentials: "include" })
    );
    expect(JSON.parse(fetchMock.mock.calls[0][1].body)).toEqual({
      title: "Comprar pão",
      due_date: "2026-09-10",
    });
  });

  it("envia POST com prioridade high", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: {
          id: 1,
          title: "Entrega urgente",
          done: false,
          archived: false,
          due_date: null,
          priority: "high",
        },
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await createTask({ title: "Entrega urgente", priority: "high" });

    expect(task.priority).toBe("high");
    expect(JSON.parse(fetchMock.mock.calls[0][1].body)).toEqual({
      title: "Entrega urgente",
      due_date: null,
      priority: "high",
    });
  });

  it("envia PATCH para atualizar prioridade com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: {
          id: 4,
          title: "Relatório",
          done: false,
          archived: false,
          due_date: null,
          priority: "low",
        },
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const task = await updateTaskPriority(4, "low");

    expect(task.priority).toBe("low");
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/tasks/4/priority"),
      expect.objectContaining({ method: "PATCH", credentials: "include" })
    );
    expect(JSON.parse(fetchMock.mock.calls[0][1].body)).toEqual({ priority: "low" });
  });

  it("envia PATCH para arquivar tarefa com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: { id: 3, title: "Relatório", done: false, archived: true, due_date: null },
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

  it("envia GET com status pending ao filtrar tarefas", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ data: [] }),
    });
    vi.stubGlobal("fetch", fetchMock);

    await listTasks("pending");

    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringMatching(/\/tasks\?status=pending$/),
      expect.objectContaining({ credentials: "include" })
    );
  });

  it("envia GET para lembretes de amanhã com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({
        data: [{ id: 2, title: "Prova", done: false, archived: false, due_date: "2026-09-07" }],
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const reminders = await listDueTomorrowReminders();

    expect(reminders).toHaveLength(1);
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/reminders/due-tomorrow"),
      expect.objectContaining({ credentials: "include" })
    );
  });
});
