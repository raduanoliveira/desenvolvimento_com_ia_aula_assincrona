import { endSession, getSession, githubStartUrl, googleStartUrl } from "./authApi";

describe("authApi", () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it("busca GET /session com credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      json: async () => ({
        data: { id: 1, name: "Ana Silva", email: "ana@example.com" },
      }),
    });
    vi.stubGlobal("fetch", fetchMock);

    const user = await getSession();

    expect(user).toEqual({ id: 1, name: "Ana Silva", email: "ana@example.com" });
    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/session"),
      expect.objectContaining({
        credentials: "include",
        headers: expect.objectContaining({ Accept: "application/json" }),
      })
    );
  });

  it("encerra a sessão com DELETE /session e credentials include", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 204,
    });
    vi.stubGlobal("fetch", fetchMock);

    await endSession();

    expect(fetchMock).toHaveBeenCalledWith(
      expect.stringContaining("/session"),
      expect.objectContaining({
        method: "DELETE",
        credentials: "include",
      })
    );
  });

  it("devolve null quando a sessão responde 401", async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: false,
      status: 401,
      json: async () => ({ message: "Não autenticado." }),
    });
    vi.stubGlobal("fetch", fetchMock);

    expect(await getSession()).toBeNull();
  });

  it("expõe githubStartUrl no mesmo host que o Google, sem /api", () => {
    expect(githubStartUrl).toBe("http://localhost:8000/auth/github");
    expect(googleStartUrl).toBe("http://localhost:8000/auth/google");
    expect(githubStartUrl).not.toContain("/api/");
  });
});
