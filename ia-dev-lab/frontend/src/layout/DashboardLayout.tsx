import AssignmentIcon from "@mui/icons-material/Assignment";
import AppBar from "@mui/material/AppBar";
import Avatar from "@mui/material/Avatar";
import Box from "@mui/material/Box";
import Chip from "@mui/material/Chip";
import Container from "@mui/material/Container";
import Stack from "@mui/material/Stack";
import Toolbar from "@mui/material/Toolbar";
import Tooltip from "@mui/material/Tooltip";
import Typography from "@mui/material/Typography";
import type { ReactNode } from "react";

export function DashboardLayout({
  title,
  subtitle,
  children,
}: {
  title: string;
  subtitle?: string;
  children: ReactNode;
}) {
  return (
    <Box sx={{ minHeight: "100vh", bgcolor: "background.default" }}>
      <Box
        sx={{
          background: "linear-gradient(135deg, #312e81 0%, #4f46e5 45%, #0d9488 100%)",
          pb: 12,
        }}
      >
        <AppBar position="static" color="transparent" elevation={0}>
          <Toolbar>
            <Stack direction="row" alignItems="center" spacing={1.5} sx={{ flexGrow: 1 }}>
              <Avatar sx={{ bgcolor: "rgba(255,255,255,0.18)" }}>
                <AssignmentIcon />
              </Avatar>
              <Typography component="h1" variant="h6" color="common.white">
                ToDo list
              </Typography>
              <Chip
                label="Tarefas"
                size="small"
                sx={{ bgcolor: "rgba(255,255,255,0.16)", color: "#fff" }}
              />
            </Stack>
            <Chip label="Online" size="small" sx={{ bgcolor: "#fff", mr: 1.5 }} />
            <Tooltip title="Conta">
              <Avatar sx={{ bgcolor: "#22d3ee", color: "#0f172a" }}>T</Avatar>
            </Tooltip>
          </Toolbar>
        </AppBar>
        <Container maxWidth="md" sx={{ pt: 4 }}>
          <Typography variant="h3" component="h2" fontWeight={800} color="common.white" sx={{ letterSpacing: "-0.04em" }}>
            {title}
          </Typography>
          {subtitle ? (
            <Typography sx={{ mt: 1, color: "rgba(255,255,255,0.82)", fontSize: "1.1rem" }}>
              {subtitle}
            </Typography>
          ) : null}
        </Container>
      </Box>
      <Container maxWidth="md" sx={{ mt: -8, pb: 6 }}>
        {children}
      </Container>
    </Box>
  );
}
