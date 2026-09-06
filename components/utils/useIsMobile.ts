import { useMediaQuery, useTheme } from '@mui/material';

// True when the viewport is below the 'sm' breakpoint (mobile).
export const useIsMobile = (): boolean => {
    const theme = useTheme();
    return useMediaQuery(theme.breakpoints.down('sm'));
}
