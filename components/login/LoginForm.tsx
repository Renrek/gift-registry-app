import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import './LoginForm.scss';
import { observer } from 'mobx-react';
import { registerComponent } from '../component.loader';
import { Box, Button, FormControl, IconButton, Input, InputAdornment, InputLabel } from '@mui/material';
import { Visibility, VisibilityOff } from '@mui/icons-material';
import { action, makeObservable, observable } from 'mobx';
import axios from 'axios';

registerComponent('login-form', (element, parameters) => {
    const controller = new LoginController();
    ReactDOMClient.createRoot(element).render(<LoginForm controller={controller} />)
});

class LoginController {

    @observable public email: string = '';

    @observable public password: string = '';

    @observable public showPassword: boolean = false;

    constructor() {
        makeObservable(this);
    }

    @action
    public updateEmail = (email : string): void => {
        this.email = email;
    }

    @action
    public updatePassword = (password : string): void => {
        this.password = password;
    }

    @action
    public updateShowPassword = (state: boolean): void => {
        this.showPassword = state;
    }

    @action
    public submit = (): void => {
        axios.post('/api/login', {
            username: this.email,
            password: this.password
        }).then((res) => console.log(res));
        // axios.post('/api/login', {

        // }, {
        //     headers: {

        //     }
        // });
    }

}

const LoginForm : React.FC<{controller: LoginController}> = observer(({controller}) => {
    return (<Box
        component="form"
        autoComplete="off"
        style={{
            margin: '1em',
            display: 'flex',
            gap: '1em',
            flexDirection: 'column',
        }}
    >
        <FormControl>
            <InputLabel htmlFor="email">Email</InputLabel>
            <Input 
                id="email" 
                type="email" 
                onChange={(e) => controller.updateEmail(e.target.value)}
            />
        </FormControl>
        <FormControl>
            <InputLabel htmlFor="password">Password</InputLabel>
            <Input 
                id="password" 
                type={controller.showPassword ? 'text' : 'password'}
                value={controller.password} 
                onChange={(e) => controller.updatePassword(e.target.value)}
                endAdornment={
                    <InputAdornment position="end">
                    <IconButton
                        aria-label="toggle password visibility"
                        onClick={() => controller.updateShowPassword(!controller.showPassword)}
                        onMouseDown={(event: React.MouseEvent<HTMLButtonElement>) => {
                            event.preventDefault();
                        }}
                    >
                        {controller.showPassword ? <VisibilityOff /> : <Visibility />}
                    </IconButton>
                    </InputAdornment>
                }
            />
        </FormControl>
        
        <Button
            variant='contained'
            onClick={() => controller.submit()}
        >
            Create Account
        </Button>
    </Box>);
});