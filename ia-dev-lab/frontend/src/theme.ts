import { createTheme } from "@mui/material/styles";

const theme = createTheme({
  palette: {
    primary: { main: "#6366f1" },
    secondary: { main: "#14b8a6" },
    background: { default: "#f8fafc", paper: "#ffffff" },
    text: { primary: "#0f172a", secondary: "#64748b" },
  },
  shape: { borderRadius: 16 },
  typography: {
    fontFamily: '"Plus Jakarta Sans", Roboto, Helvetica, Arial, sans-serif',
    h4: { fontWeight: 700, letterSpacing: "-0.03em" },
    h6: { fontWeight: 700 },
    button: { textTransform: "none", fontWeight: 600 },
  },
  components: {
    MuiAppBar: {
      styleOverrides: {
        root: {
          backgroundColor: "rgba(248, 250, 252, 0.86)",
          color: "#0f172a",
          boxShadow: "none",
          backdropFilter: "blur(10px)",
          borderBottom: "1px solid #e2e8f0",
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        contained: { boxShadow: "none", paddingInline: 20, paddingBlock: 10 },
      },
    },
    MuiCard: {
      defaultProps: { elevation: 0 },
      styleOverrides: {
        root: {
          border: "1px solid #e2e8f0",
          boxShadow: "0 10px 30px rgba(15, 23, 42, 0.04)",
        },
      },
    },
    MuiDrawer: {
      styleOverrides: {
        paper: {
          backgroundColor: "#0f172a",
          color: "#e2e8f0",
          borderRight: "none",
        },
      },
    },
  },
});

export default theme;
