import * as React from 'react';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import AddIcon from '@mui/icons-material/Add';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField } from '@mui/material';
import axios from 'axios';
import Notification from '../../utils/notification';


export class InvitationDialogController {

    @observable public email: string = '';
    @observable public isOpen: boolean = false;

    constructor(
      public readonly createInvitationUrl: string
    ) {
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
        axios.post(this.createInvitationUrl, {
          email: this.email
        }).then((res) => {
          Notification.success('Invitation created');
          this.toggleDialog();
        }).catch((err) => {
          Notification.error('Failed to create invitation: ' + err.response.data.message);
        });
    }
}

export const InviteDialog : React.FC<{controller: InvitationDialogController}> = observer(({controller}) => {

  return <React.Fragment>
    <Button variant="contained" onClick={controller.toggleDialog}>
      <AddIcon /> Create Invitation
    </Button>
    <Dialog
        open={controller.isOpen}
        onClose={controller.toggleDialog}
        PaperProps={{component: 'div'}}
    >
      <DialogTitle>Create Invitation</DialogTitle>
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