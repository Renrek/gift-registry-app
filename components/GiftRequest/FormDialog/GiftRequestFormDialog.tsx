import * as React from 'react';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField } from '@mui/material';
import axios from 'axios';
import { GiftRequestDTO, GiftRequestListItemDTO, NewGiftRequestDTO } from '../../types';
import AddIcon from '@mui/icons-material/Add';

export class GiftRequestFormDialogController {

    @observable public isOpen: boolean = false;

    @observable public giftRequest: NewGiftRequestDTO = {
        name: '',
        description: '',
    }

    constructor(
        private callback: (result: GiftRequestListItemDTO) => void,
    ) {
        makeObservable(this);
    }

    @action
    public updateGiftRequest = (giftRequest: Partial<NewGiftRequestDTO>): void => {
        this.giftRequest = {
            ...this.giftRequest,
            ...giftRequest,
        }
    }

    @action
    public toggleDialog = (): void => {
        this.isOpen = !this.isOpen;
    }

    @action
    public submit = async (): Promise<void> => {
        await axios.post('/gift-request/add', {
            name: this.giftRequest.name,
            description: this.giftRequest.description,
        }).then((result) => {
            this.callback(result.data);
        });
        this.toggleDialog();
    }
    
}

export const GiftRequestFormDialog: React.FC<{ 
    controller: GiftRequestFormDialogController 
}> = observer(({ controller }) => {
    return (
        <React.Fragment>
            <Button variant="contained" onClick={controller.toggleDialog}>
                <AddIcon /> Create Gift Request
            </Button>
            <Dialog
                open={controller.isOpen}
                onClose={controller.toggleDialog}
                PaperProps={{ component: 'div' }}
            >
                <DialogTitle>Create Gift Request</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        To create a new gift request, please enter the name and description here.
                    </DialogContentText>
                    <TextField
                        autoFocus
                        required
                        margin="dense"
                        id="name"
                        name="name"
                        label="Name"
                        type="text"
                        fullWidth
                        variant="standard"
                        value={controller.giftRequest.name}
                        onChange={(e) => controller.updateGiftRequest({name: e.target.value})}
                    />
                    <TextField
                        required
                        margin="dense"
                        id="description"
                        name="description"
                        label="Description"
                        type="text"
                        fullWidth
                        variant="standard"
                        value={controller.giftRequest.description}
                        onChange={(e) => controller.updateGiftRequest({description: e.target.value})}
                    />
                </DialogContent>
                <DialogActions>
                    <Button onClick={controller.toggleDialog}>Cancel</Button>
                    <Button onClick={controller.submit}>
                        Add Gift Request
                    </Button>
                </DialogActions>
            </Dialog>
        </React.Fragment>
    );
});