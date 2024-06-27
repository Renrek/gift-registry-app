import { AppBar, Box, Button, Menu, Toolbar, Typography } from '@mui/material';
import { makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../component.loader';
import axios from 'axios';

registerComponent('main-navigation', (element, parameters) => {
    const [ isLoggedin ] = parameters;
    const controller = new MainNavigationController(isLoggedin);
    ReactDOMClient.createRoot(element).render(
        <MainNavigation controller={controller} />
    );
});

class MainNavigationController {

    constructor(
        public readonly isLoggedIn: boolean,
    ){
        makeObservable(this);
    }

}



const MainNavigation : React.FC<{
    controller: MainNavigationController
}> = observer(({controller}) => {
    const handleLogOut = () => {
        window.open("/logout", "_self")
    }
    return <AppBar position='static'>
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
            {controller.isLoggedIn && <Button 
                color="inherit"
                onClick={handleLogOut}
            >Logout</Button> }
        </Toolbar>
    </AppBar>;
});