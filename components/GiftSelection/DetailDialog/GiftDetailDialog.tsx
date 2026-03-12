import * as React from 'react';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogTitle, Box } from '@mui/material';
import axios from 'axios';
import { GiftSelectionPanelItemDTO } from '../../types';
import Notification from '../../utils/notification';

export class GiftDetailDialogController {

    @observable public isOpen: boolean = false;

    public gift: GiftSelectionPanelItemDTO | null = null;

    constructor(
        private onClaim: (gift: GiftSelectionPanelItemDTO) => void,
    ) {
        makeObservable(this);
    }

    @action
    public openDialog = (gift: GiftSelectionPanelItemDTO): void => {
        this.gift = gift;
        this.isOpen = true;
    }

    @action
    public closeDialog = (): void => {
        this.isOpen = false;
        this.gift = null;
    }

    @action
    public claim = async (): Promise<void> => {
        if (!this.gift) return;
        
        try {
            await axios.post(this.gift.claimUrl);
            Notification.success('Gift claimed successfully!');
            this.onClaim(this.gift);
            this.closeDialog();
        } catch (error) {
            Notification.error('Failed to claim gift');
        }
    }
}

export const GiftDetailDialog: React.FC<{ 
    controller: GiftDetailDialogController 
}> = observer(({ controller }) => {
    return (
        <Dialog
            open={controller.isOpen && !!controller.gift}
            onClose={controller.closeDialog}
            disableEnforceFocus
            maxWidth="sm"
            fullWidth
            PaperProps={{ component: 'div' }}
        >
            {controller.gift && (
                <>
                    <DialogTitle>{controller.gift.name}</DialogTitle>
                    <DialogContent>
                        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                            {controller.gift.imagePath && (
                                <img
                                    src={`/uploads/${controller.gift.imagePath}`}
                                    alt={controller.gift.name}
                                    style={{ maxWidth: '100%', height: 'auto', borderRadius: 4 }}
                                />
                            )}
                            <Box>
                                <div style={{ fontSize: '0.95em', fontWeight: 500, marginBottom: '8px' }}>
                                    Description:
                                </div>
                                <p style={{ margin: 0, color: '#555' }}>
                                    {controller.gift.description}
                                </p>
                            </Box>
                        </Box>
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={controller.closeDialog}>Cancel</Button>
                        <Button 
                            onClick={controller.claim}
                            variant="contained"
                            color="success"
                        >
                            Claim Gift
                        </Button>
                    </DialogActions>
                </>
            )}
        </Dialog>
    );
});
