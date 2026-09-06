import React from 'react';
import { registerComponent, renderWithTheme } from '../../component.loader';
import { observer } from 'mobx-react';
import { action, makeObservable, observable } from 'mobx';
import { GiftSelectionPanelConfig, GiftSelectionPanelItemDTO } from '../../types';
import { Box, Button, Card, CardContent, CardMedia } from '@mui/material';
import { GiftDetailDialogController, GiftDetailDialog } from '../DetailDialog/GiftDetailDialog';

registerComponent('gift-selection-panel', (element, parameters) => {
    const [ config ] = parameters;
    const controller = new GiftSelectionPanelController(config);
    renderWithTheme(element, <GiftSelectionPanel controller={controller}/>);
});

class GiftSelectionPanelController {

    @observable
    public gifts: GiftSelectionPanelItemDTO[];

    public detailDialogController: GiftDetailDialogController;

    constructor(
        config: GiftSelectionPanelConfig
    ) {
        makeObservable(this);
        this.gifts = config.gifts;
        this.detailDialogController = new GiftDetailDialogController(
            (gift) => this.onGiftClaimed(gift)
        );
    }

    @action
    public openGiftDetail = (gift: GiftSelectionPanelItemDTO): void => {
        this.detailDialogController.openDialog(gift);
    }

    @action
    private onGiftClaimed = (gift: GiftSelectionPanelItemDTO): void => {
        // Remove the claimed gift from the list
        this.gifts = this.gifts.filter(g => g.giftId !== gift.giftId);
    }
}

const GiftSelectionPanel: React.FC<{
    controller: GiftSelectionPanelController
}> = observer(({controller}) => {

    return (
        <Box>
            <Box sx={{ marginBottom: 2 }}>
                <h2>Available Gifts</h2>
            </Box>
            
            <GiftDetailDialog controller={controller.detailDialogController} />

            <Box 
                sx={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
                    gap: 2,
                }}
            >
                {controller.gifts && controller.gifts.map((gift) => (
                    <Card 
                        key={gift.giftId}
                        sx={{
                            display: 'flex',
                            flexDirection: 'column',
                            height: '100%',
                        }}
                    >
                        {gift.imagePath && (
                            <CardMedia
                                component="img"
                                height="200"
                                image={`/uploads/${gift.imagePath}`}
                                alt={gift.name}
                                sx={{ objectFit: 'cover' }}
                            />
                        )}
                        <CardContent sx={{ flexGrow: 1 }}>
                            <h3 style={{ margin: '0 0 8px 0' }}>
                                {gift.name}
                            </h3>
                        </CardContent>
                        <Box sx={{ padding: 2, paddingTop: 0 }}>
                            <Button 
                                fullWidth
                                variant="contained"
                                onClick={() => controller.openGiftDetail(gift)}
                            >
                                View Details
                            </Button>
                        </Box>
                    </Card>
                ))}
            </Box>
        </Box>
    );
});