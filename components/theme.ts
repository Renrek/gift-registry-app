import { createTheme } from '@mui/material/styles';

// Shared MUI theme; primary color matches Bootstrap's $primary in main.scss.
export const theme = createTheme({
    palette: {
        primary: {
            main: '#008000',
        },
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    minHeight: 44,
                },
            },
        },
        MuiIconButton: {
            styleOverrides: {
                root: {
                    minWidth: 44,
                    minHeight: 44,
                },
            },
        },
    },
});
