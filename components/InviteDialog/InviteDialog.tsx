import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../component.loader';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField } from '@mui/material';
import axios from 'axios';

registerComponent('invitation-dialog-button', (element, parameters) => {   
    const controller = new InviteController();
    ReactDOMClient.createRoot(element).render(<InviteDialog controller={controller} />)
});

class InviteController {

    @observable public email: string = '';
    @observable public isOpen: boolean = false;

    constructor() {
        makeObservable(this);
    }

    @action
    public updateEmail = (email : string): void => {
        this.email = email;
    }

    @action
    public toggleDialog = (): void => {
        this.isOpen = !this.isOpen;
    }

    @action
    public submit = (): void => {
        axios.post('/invitation/create', {
            email: this.email
        }).then((res) => {
            // window.open("/gift-request", "_self");
            //window.location.reload();
            this.toggleDialog();
        });
    }
}

const InviteDialog : React.FC<{controller: InviteController}> = observer(({controller}) => {

    return <React.Fragment>
    <Button variant="contained" onClick={controller.toggleDialog}>
        Create Invitation
    </Button>
    <Dialog
        open={controller.isOpen}
        onClose={controller.toggleDialog}
        PaperProps={{
            component: 'div',
            // onSubmit: (event: React.FormEvent<HTMLFormElement>) => {
            //     event.preventDefault();
            //     const formData = new FormData(event.currentTarget);
            //     const formJson = Object.fromEntries((formData as any).entries());
            //     const email = formJson.email;
            //     axios.post('/invitation/create', {
            //         username: email
            //     }).then((res) => {
            //         // window.open("/gift-request", "_self");
            //         //window.location.reload();
            //         controller.toggleDialog();
            //     });
            // },
      }}
    >
      <DialogTitle>Subscribe</DialogTitle>
      <DialogContent>
        <DialogContentText>
          To invite someone to this website, please enter their address here.
        </DialogContentText>
        <TextField
          autoFocus
          required
          margin="dense"
          id="email"
          name="email"
          label="Email Address"
          type="email"
          fullWidth
          variant="standard"
          value={controller.email}
          onChange={(e) => controller.updateEmail(e.target.value)}
        />
      </DialogContent>
      <DialogActions>
        <Button onClick={controller.toggleDialog}>Cancel</Button>
        <Button 
            onClick={controller.submit}
        >
            Invite
        </Button>
      </DialogActions>
    </Dialog>
  </React.Fragment>;
});