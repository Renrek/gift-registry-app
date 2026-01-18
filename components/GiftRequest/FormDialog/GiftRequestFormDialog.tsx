import * as React from 'react';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle, TextField, Box } from '@mui/material';
import axios from 'axios';
import { GiftRequestDTO, NewGiftRequestDTO } from '../../types';
import AddIcon from '@mui/icons-material/Add';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import Notification from '../../utils/notification';
import UserAction from '../../utils/userAction';
import { ImageResizer } from '../../utils/ImageResizer';

export class GiftRequestFormDialogController {

    private initialGiftRequest: GiftRequestDTO | null = null;

    @observable public isOpen: boolean = false;

    @observable public imageFile: File | null = null;

    @observable public removeImage: boolean = false;

    @observable public giftRequest: NewGiftRequestDTO = {
        name: '',
        description: '',
        imagePath: '',
        imageBase64: ''
    }

    @observable public imagePath: string | null = null;

    @observable public deletePath: string | null = null;

    public isEditMode: boolean = false;

    constructor(
        private onUpdate: (result: GiftRequestDTO) => void,
        private onDelete: (gift: GiftRequestDTO) => void,
        public submitURL: string,
        initialData?: GiftRequestDTO
    ) {
        if (initialData) {
            this.isEditMode = true;
            this.giftRequest = {
                name: initialData.name,
                description: initialData.description,
                imagePath: '',
                imageBase64: ''
            };
            this.imagePath = initialData.imagePath || null;
            this.deletePath = initialData.deletePath || null;
            this.initialGiftRequest = initialData;
        }
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
    public setRemoveImage = (remove: boolean): void => {
        this.removeImage = remove;
        if (remove) {
            this.imagePath = null;
            this.imageFile = null;
        }
    }

    @action
    public delete = async (): Promise<void> => {
        if (!this.deletePath || !this.initialGiftRequest) return;
        await axios.delete(this.deletePath).then(() => {
            Notification.success('Gift request deleted successfully');
            this.onDelete(this.initialGiftRequest);
        });
        this.toggleDialog();
    }

    @action
    public submit = async (): Promise<void> => {
        let imageBase64: string | null = null;
        if (this.imageFile) {
            // You can customize options here or use defaults
            const resizer = new ImageResizer({ maxWidth: 800, maxHeight: 800, quality: 0.7 });
            imageBase64 = await resizer.resizeFileToBase64(this.imageFile);
        }
        const payload: any = {
            name: this.giftRequest.name,
            description: this.giftRequest.description,
            imageBase64: imageBase64,
        };
        
        if (this.isEditMode) {
            payload.removeImage = this.removeImage;
        }

        await axios.post(this.submitURL, payload).then((result) => {
            const message = this.isEditMode ? 'Gift request updated successfully' : 'Gift request created successfully';
            Notification.success(message);
            // Reset the dialog state after successful submission
            this.imageFile = null;
            this.removeImage = false;
            // Update imagePath with the response from server
            this.imagePath = result.data.imagePath || null;
            this.onUpdate(result.data);
        });
        this.toggleDialog();
    }
    
}

export const GiftRequestFormDialog: React.FC<{ 
    controller: GiftRequestFormDialogController 
}> = observer(({ controller }) => {
    const [previewUrl, setPreviewUrl] = React.useState<string | null>(null);
    const fileInputRef = React.useRef<HTMLInputElement>(null);

    React.useEffect(() => {
        if (controller.imageFile) {
            const url = URL.createObjectURL(controller.imageFile);
            setPreviewUrl(url);
            return () => URL.revokeObjectURL(url);
        } else {
            setPreviewUrl(null);
        }
    }, [controller.imageFile]);

    const title = controller.isEditMode ? 'Edit Gift Request' : 'Create Gift Request';
    const description = controller.isEditMode 
        ? 'To update the gift request, please modify the name and description here.'
        : 'To create a new gift request, please enter the name and description here.';
    const buttonText = controller.isEditMode ? 'Update Gift Request' : 'Add Gift Request';
    const triggerButtonText = controller.isEditMode ? 'Edit' : 'Create Gift Request';
    const triggerIcon = controller.isEditMode ? <EditIcon /> : <AddIcon />;

    const handleUploadClick = (e: React.MouseEvent<HTMLElement>) => {
        e.preventDefault();
        fileInputRef.current?.click();
    };

    return (
        <React.Fragment>
            <Button 
                variant="contained" 
                onClick={controller.toggleDialog}
                size={controller.isEditMode ? 'small' : 'medium'}
            >
                {triggerIcon} {triggerButtonText}
            </Button>
            <Dialog
                open={controller.isOpen}
                onClose={controller.toggleDialog}
                disableEnforceFocus
                PaperProps={{ component: 'div' }}
            >
                <DialogTitle>{title}</DialogTitle>
                <DialogContent>
                    <DialogContentText>
                        {description}
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
                    
                    {/* Current Image Section (Edit Mode) */}
                    {controller.isEditMode && controller.imagePath && !controller.removeImage && (
                        <div style={{ margin: '16px 0' }}>
                            <div style={{ fontSize: '0.95em', fontWeight: 500, marginBottom: '8px' }}>
                                Current Image:
                            </div>
                            <img
                                src={`/uploads/${controller.imagePath}`}
                                alt="Current"
                                style={{ maxWidth: 150, maxHeight: 150, display: 'block', marginBottom: 8, borderRadius: 4, border: '1px solid #ccc' }}
                            />
                            <Button 
                                variant="outlined" 
                                color="error"
                                size="small"
                                onClick={() => controller.setRemoveImage(true)}
                            >
                                <DeleteIcon /> Delete Image
                            </Button>
                        </div>
                    )}

                    {/* New/Selected Image Section */}
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
                        ref={fileInputRef}
                        accept="image/*"
                        style={{ display: 'none' }}
                        id="image-upload"
                        type="file"
                        onChange={(e) => {
                            const file = e.target.files?.[0] || null;
                            controller.setImageFile(file);
                        }}
                    />
                    <Button 
                        variant="contained" 
                        onClick={handleUploadClick}
                    >
                        Upload Image
                    </Button>
                </DialogContent>
                <DialogActions>
                    {controller.isEditMode && controller.deletePath && (
                        <Button 
                            variant="contained"
                            color="error"
                            onClick={async () => {
                                if (await UserAction.confirm('Are you sure you want to delete this gift request?')) {
                                    await controller.delete();
                                }
                            }}
                        >
                            Delete
                        </Button>
                    )}
                    <Box sx={{ flex: 1 }} />
                    <Button onClick={controller.toggleDialog}>Cancel</Button>
                    <Button onClick={controller.submit}>
                        {buttonText}
                    </Button>
                </DialogActions>
            </Dialog>
        </React.Fragment>
    );
});