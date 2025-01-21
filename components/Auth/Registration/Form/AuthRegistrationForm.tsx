import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../../../component.loader";
import { observer } from 'mobx-react';
import { Box, Button, FormControl, FormHelperText, IconButton, Input, InputAdornment, InputLabel } from '@mui/material';
import { action, makeObservable, observable } from 'mobx';
import Visibility from '@mui/icons-material/Visibility';
import VisibilityOff from '@mui/icons-material/VisibilityOff';
import axios from 'axios';

registerComponent('registration-form', (element, parameters) => {
    const controller = new AuthRegistrationFormController();
    ReactDOMClient.createRoot(element).render(<AuthRegistrationForm controller={controller} />)
});

class AuthRegistrationFormController {

    @observable public email: string = '';

    @observable public emailConfirm: string = '';

    @observable public inviteCode: string = '';

    @observable public password: string = '';

    @observable public passwordConfirm: string = '';

    @observable public showPassword: boolean = false;

    @observable public showConfirmPassword: boolean = false;

    @observable public message: string = '';

    constructor(){
        makeObservable(this);
    }

    @action
    public updateEmail = (email : string): void => {
        this.email = email;
    }

    @action
    public updateEmailConfirm = (email : string): void => {
        this.emailConfirm = email;
    }

    @action
    public updateInviteCode = (code : string): void => {
        this.inviteCode = code;
    }

    @action
    public updatePassword = (password : string): void => {
        this.password = password;
    }

    @action
    public updatePasswordConfirm = (password : string): void => {
        this.passwordConfirm = password;
    }

    @action
    public updateShowPassword = (state: boolean): void => {
        this.showPassword = state;
    }

    @action
    public updateShowConfirmPassword = (state: boolean): void => {
        this.showConfirmPassword = state;
    }

    public submitNewUser = () => {
        axios.post('/registration/create', {
            email: this.email,
            password: this.password,
            invitationCode: this.inviteCode
        }).then((res) => {
            window.open("/", "_self");
        });
    }
    
}

const AuthRegistrationForm: React.FC<{controller: AuthRegistrationFormController}> = observer(({controller}) => {
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
                value={controller.email} 
                onChange={(e) => controller.updateEmail(e.target.value)}
            />
        </FormControl>
        <FormControl>
            <InputLabel htmlFor="email-confirm">Email Confirm</InputLabel>
            <Input 
                id="email-confirm" 
                type="email"
                value={controller.emailConfirm} 
                onChange={(e) => controller.updateEmailConfirm(e.target.value)}
            />
        </FormControl>
        <FormControl>
            <InputLabel htmlFor="invite-code">Invite Code</InputLabel>
            <Input 
                id="invite-code" 
                value={controller.inviteCode} 
                onChange={(e) => controller.updateInviteCode(e.target.value)}
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
        <FormControl>
            <InputLabel htmlFor="password-confirm">Password Confirm</InputLabel>
            <Input 
                id="password-confirm" 
                type={controller.showConfirmPassword ? 'text' : 'password'}
                value={controller.passwordConfirm}
                onChange={(e) => controller.updatePasswordConfirm(e.target.value)}
                endAdornment={
                    <InputAdornment position="end">
                    <IconButton
                        aria-label="toggle password visibility"
                        onClick={() => controller.updateShowConfirmPassword(!controller.showConfirmPassword)}
                        onMouseDown={(event: React.MouseEvent<HTMLButtonElement>) => {
                            event.preventDefault();
                        }}
                    >
                        {controller.showConfirmPassword ? <VisibilityOff /> : <Visibility />}
                    </IconButton>
                    </InputAdornment>
                }
            />
        </FormControl>
        <Button
            variant='contained'
            onClick={controller.submitNewUser}
        >
            Create Account
        </Button>
        <div><p>{controller.message}</p></div>
    </Box>);
});