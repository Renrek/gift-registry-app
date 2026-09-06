import { AppBar, BottomNavigation, BottomNavigationAction, Box, Button, Paper, Toolbar, Typography } from '@mui/material';
import HomeIcon from '@mui/icons-material/Home';
import PersonIcon from '@mui/icons-material/Person';
import AssignmentIcon from '@mui/icons-material/Assignment';
import LogoutIcon from '@mui/icons-material/Logout';
import { makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import { registerComponent, renderWithTheme } from '../../component.loader';
import axios from 'axios';

registerComponent('main-navigation', (element, parameters) => {
    const [ isLoggedin ] = parameters;
    const controller = new NavigationMainController(isLoggedin);
    renderWithTheme(element, <NavigationMain controller={controller} />);
});

class NavigationMainController {

    constructor(
        public readonly isLoggedIn: boolean,
    ){
        makeObservable(this);
    }

}

const NavigationMain : React.FC<{
    controller: NavigationMainController
}> = observer(({controller}) => {
    const handleLogOut = () => {
        window.open("/logout", "_self")
    }
    return <>
        <AppBar position='static'>
            <Toolbar
                disableGutters
                sx={{padding: "1em"}}
            >
                <Typography
                    variant="h6"
                    component="div"
                    sx={{ flexGrow: 1}}
                >
                Gift Registry App
                </Typography>
                <Box sx={{ display: { xs: 'none', sm: 'block' } }}>
                    {controller.isLoggedIn && <Button
                        color="inherit"
                        href="/"
                    >Home</Button>}
                    {controller.isLoggedIn && <Button
                        color="inherit"
                        href="/profile"
                    >Profile</Button>}
                    {controller.isLoggedIn && <Button
                        color="inherit"
                        href="/gift-requests"
                    >My Gift Requests</Button>}
                    {controller.isLoggedIn && <Button
                        color="inherit"
                        onClick={handleLogOut}
                    >Logout</Button> }
                </Box>
            </Toolbar>
        </AppBar>
        {controller.isLoggedIn && <Paper
            elevation={3}
            sx={{
                display: { xs: 'block', sm: 'none' },
                position: 'fixed',
                bottom: 0,
                left: 0,
                right: 0,
                zIndex: (theme) => theme.zIndex.appBar,
            }}
        >
            <BottomNavigation showLabels>
                <BottomNavigationAction label="Home" icon={<HomeIcon />} href="/" />
                <BottomNavigationAction label="Profile" icon={<PersonIcon />} href="/profile" />
                <BottomNavigationAction label="Gift Requests" icon={<AssignmentIcon />} href="/gift-requests" />
                <BottomNavigationAction label="Logout" icon={<LogoutIcon />} onClick={handleLogOut} />
            </BottomNavigation>
        </Paper>}
    </>;
});
