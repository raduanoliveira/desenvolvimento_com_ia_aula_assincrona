import GoogleIcon from "@mui/icons-material/Google";
import Alert from "@mui/material/Alert";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Card from "@mui/material/Card";
import CardContent from "@mui/material/CardContent";
import Container from "@mui/material/Container";
import Stack from "@mui/material/Stack";
import Typography from "@mui/material/Typography";
import { useAuthContext } from "./AuthContext";

export function SignInScreen() {
  const { error, googleStartUrl } = useAuthContext();

  return (
    <Box
      sx={{
        minHeight: "100vh",
        background: "linear-gradient(135deg, #312e81 0%, #4f46e5 45%, #0d9488 100%)",
        display: "flex",
        alignItems: "center",
      }}
    >
      <Container maxWidth="sm">
        <Stack spacing={3}>
          <Box>
            <Typography variant="overline" sx={{ color: "rgba(255,255,255,0.8)", letterSpacing: "0.16em" }}>
              ToDo list
            </Typography>
            <Typography variant="h3" component="h1" fontWeight={800} color="common.white" sx={{ letterSpacing: "-0.04em" }}>
              Entre para ver as suas tarefas
            </Typography>
            <Typography sx={{ mt: 1.5, color: "rgba(255,255,255,0.82)", fontSize: "1.05rem" }}>
              Só você vê e altera a sua lista. Continuar com a conta Google.
            </Typography>
          </Box>
          <Card sx={{ borderRadius: 4, boxShadow: 8 }}>
            <CardContent sx={{ p: 4 }}>
              <Stack spacing={2.5}>
                {error ? (
                  <Alert severity="info" role="alert">
                    {error}
                  </Alert>
                ) : null}
                <Button
                  href={googleStartUrl}
                  variant="contained"
                  size="large"
                  startIcon={<GoogleIcon />}
                  fullWidth
                >
                  Continuar com Google
                </Button>
              </Stack>
            </CardContent>
          </Card>
        </Stack>
      </Container>
    </Box>
  );
}
