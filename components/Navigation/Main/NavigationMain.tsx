import { AppBar, Box, Button, Menu, Toolbar, Typography } from '@mui/material';
import { makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../../component.loader';
import axios from 'axios';

registerComponent('main-navigation', (element, parameters) => {
    const [ isLoggedin ] = parameters;
    const controller = new NavigationMainController(isLoggedin);
    ReactDOMClient.createRoot(element).render(
        <NavigationMain controller={controller} />
    );
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
            <div>
                {controller.isLoggedIn && <Button 
                    color="inherit"
                    href="/profile"
                >Home</Button>}
                {controller.isLoggedIn && <Button 
                    color="inherit"
                    onClick={handleLogOut}
                >Logout</Button> }
            </div>
        </Toolbar>
    </AppBar>;
});