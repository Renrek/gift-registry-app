import * as React from 'react';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField } from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import { action, makeObservable, observable } from "mobx";
import axios from 'axios';
import Notification from "../../utils/notification";
import { UserDTO } from "../../types";

export class ConnectionFormDialogController {

    @observable public isOpen: boolean = false;
    @observable public emailSearchString: string = "";
    @observable public userList: UserDTO[] = [];

    public constructor(
        public searchURL: string,
        public addURL: string
    ) {
        makeObservable(this);
    }

    @action
    public toggleDialog = (): void => {
        this.isOpen = !this.isOpen;
    }

    @action
    public updateEmail = (email: string): void => {
        this.emailSearchString = email;
    }

    @action
    public onSearch = (): void => {
        if(this.emailSearchString.length < 3) {
            Notification.error("Please enter at least 3 characters.");
            return;
        }
        axios.get(this.searchURL, {
            params: {
                emailPartial: this.emailSearchString
            }
        })
        .then((res) => {
            if (res.data.length === 0) {
                Notification.info("No users found.");
                return;
            }
            this.userList = res.data;
        }).catch((err) => {
            Notification.error("An unexpected error occurred.");
        });
    }

    @action
    public handleAdd = (user: UserDTO): void => {
        axios.post(this.addURL, user).then((res) => {
            Notification.success(`${user.email} will be added to your contacts once they accept.`);
            this.toggleDialog();
        }).catch((err) => {
            Notification.error("An unexpected error occurred.");
        });
    }
}


export const ConnectionFormDialog: React.FC<{ 
    controller: ConnectionFormDialogController 
}> = observer(({ controller }) => {
    return (
        <React.Fragment>
            <Button variant="contained" onClick={controller.toggleDialog}>
                <AddIcon /> Add Connection
            </Button>
            <Dialog
                open={controller.isOpen}
                onClose={controller.toggleDialog}
                PaperProps={{ component: 'div' }}
            >
                <DialogTitle>Add Connection</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        To add a new connection, please enter the email here.
                    </DialogContentText>
                    <TextField
                        autoFocus
                        required
                        margin="dense"
                        id="email"
                        name="email"
                        label="Email"
                        type="text"
                        fullWidth
                        variant="standard"
                        value={controller.emailSearchString}
                        onChange={(e) => controller.updateEmail(e.target.value)}
                    />
                    <Button 
                        onClick={() => {controller.onSearch()}}
                    >
                        Search
                    </Button>
                    {controller.userList && controller.userList.map((user: UserDTO) => (
                        <div key={user.id}>
                            {user.email}
                            <Button
                                onClick={() => controller.handleAdd(user)}
                            >
                                Add
                            </Button>
                        </div>
                    ))}
                </DialogContent>
                <DialogActions>
                    <Button onClick={controller.toggleDialog}>Cancel</Button>
                </DialogActions>
            </Dialog>
        </React.Fragment>
    );
});