import * as React from 'react';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField } from '@mui/material';
import axios from 'axios';
import { GiftRequestDTO, NewGiftRequestDTO } from '../../types';
import AddIcon from '@mui/icons-material/Add';
import Notification from '../../utils/notification';
import { ImageResizer } from '../../utils/ImageResizer';

export class GiftRequestFormDialogController {

    @observable public isOpen: boolean = false;

    @observable public imageFile: File | null = null;

    @observable public giftRequest: NewGiftRequestDTO = {
        name: '',
        description: '',
    }

    constructor(
        private callback: (result: GiftRequestDTO) => void,
        public addGiftRequestURL: string
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
    public setImageFile = (file: File | null): void => {
        this.imageFile = file;
    }

    @action
    public submit = async (): Promise<void> => {
        let imageBase64: string | null = null;
        if (this.imageFile) {
            // You can customize options here or use defaults
            const resizer = new ImageResizer({ maxWidth: 800, maxHeight: 800, quality: 0.7 });
            imageBase64 = await resizer.resizeFileToBase64(this.imageFile);
        }
        await axios.post(this.addGiftRequestURL, {
            name: this.giftRequest.name,
            description: this.giftRequest.description,
            imageBase64: imageBase64,
        }).then((result) => {
            Notification.success('Gift request created successfully');
            this.callback(result.data);
        });
        this.toggleDialog();
    }
    
}

export const GiftRequestFormDialog: React.FC<{ 
    controller: GiftRequestFormDialogController 
}> = observer(({ controller }) => {
    const [previewUrl, setPreviewUrl] = React.useState<string | null>(null);

    React.useEffect(() => {
        if (controller.imageFile) {
            const url = URL.createObjectURL(controller.imageFile);
            setPreviewUrl(url);
            return () => URL.revokeObjectURL(url);
        } else {
            setPreviewUrl(null);
        }
    }, [controller.imageFile]);

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
                    <br />
                    {controller.imageFile && (
                        <>
                            <div style={{ margin: '8px 0', color: '#555', fontSize: '0.95em' }}>
                                Selected image: {controller.imageFile.name}
                            </div>
                            {previewUrl && (
                                <img
                                    src={previewUrl}
                                    alt="Preview"
                                    style={{ maxWidth: 120, maxHeight: 120, display: 'block', marginBottom: 8, borderRadius: 4, border: '1px solid #ccc' }}
                                />
                            )}
                        </>
                    )}
                    <input
                        accept="image/*"
                        style={{ display: 'none' }}
                        id="image-upload"
                        type="file"
                        onChange={(e) => {
                            const file = e.target.files?.[0] || null;
                            controller.setImageFile(file);
                        }}
                    />
                    <label htmlFor="image-upload">
                        <Button variant="contained" component="span">
                            Upload Image
                        </Button>
                    </label>
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