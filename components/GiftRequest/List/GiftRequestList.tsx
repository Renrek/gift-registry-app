import React from "react";
import { registerComponent, renderWithTheme } from "../../component.loader";
import { observer } from "mobx-react";
import { action, makeObservable, observable } from "mobx";
import { Box, Card, CardContent, CardMedia, Tooltip, Typography } from "@mui/material";
import { DataGrid, GridColDef, GridToolbar } from '@mui/x-data-grid';
import { GiftRequestFormDialogController, GiftRequestFormDialog } from "../FormDialog/GiftRequestFormDialog";
import { GiftRequestDTO } from "../../types";
import { useIsMobile } from "../../utils/useIsMobile";

registerComponent('gift-request-list', (element, parameters) => {
    const [giftRequests, addGiftRequestUrl] = parameters;
    const controller = new GiftRequestListController(giftRequests, addGiftRequestUrl);
    renderWithTheme(element, <GiftRequestList controller={controller}/>);
});

type GiftRequestLineItem = {
    giftRequest: GiftRequestDTO;
    controller: GiftRequestFormDialogController;
}

class GiftRequestListController {

    @observable
    public lineItems: Array<GiftRequestLineItem> = [];

    public addGiftRequestURL: string;
    constructor(giftRequests: Array<GiftRequestDTO>, addGiftRequestURL: string)
    {
        makeObservable(this);
        this.lineItems = giftRequests.map(gr => this.createLineItem(gr));
        this.addGiftRequestURL = addGiftRequestURL;
    }

    private createLineItem(giftRequest: GiftRequestDTO): GiftRequestLineItem {
        const controller = new GiftRequestFormDialogController(
            (result) => this.updateGiftRequest(result),
            (deletedGift) => this.deleteGiftRequestFromDialog(deletedGift),
            giftRequest.editPath,
            giftRequest
        );
        return { giftRequest, controller };
    }

    @action
    addGiftRequest(giftRequest: GiftRequestDTO): void
    {
        this.lineItems = [...this.lineItems, this.createLineItem(giftRequest)];
    }

    @action
    updateGiftRequest(updatedGiftRequest: GiftRequestDTO): void
    {
        const index = this.lineItems.findIndex(li => li.giftRequest.id === updatedGiftRequest.id);
        if (index !== -1) {
            this.lineItems[index] = {
                ...this.lineItems[index],
                giftRequest: updatedGiftRequest,
            };
        }
    }

    @action
    deleteGiftRequestFromDialog(giftRequest: GiftRequestDTO): void
    {
        this.lineItems = this.lineItems.filter(li => li.giftRequest.id !== giftRequest.id);
    }

    public getEditController(giftRequest: GiftRequestDTO): GiftRequestFormDialogController {
        const item = this.lineItems.find(li => li.giftRequest.id === giftRequest.id);
        if (!item) {
            const newItem = this.createLineItem(giftRequest);
            this.lineItems = [...this.lineItems, newItem];
            return newItem.controller;
        }
        return item.controller;
    }

}

const GiftRequestEditCell: React.FC<{ listController: GiftRequestListController; giftRequest: GiftRequestDTO }> = ({ listController, giftRequest }) => {
    return <GiftRequestFormDialog controller={listController.getEditController(giftRequest)} />;
};

const GiftRequestList : React.FC<{
    controller: GiftRequestListController
}> = observer(({controller}) => {

    const isMobile = useIsMobile();

    const giftRequestCreateDialogController = new GiftRequestFormDialogController(
        (result) => {controller.addGiftRequest(result)},
        () => {},
        controller.addGiftRequestURL
    );

    const columns: GridColDef[] = [
        { field: 'name', headerName: 'Name', flex: 1, width: 150 },
        { field: 'description', headerName: 'Description', flex: 3, width: 150 },
        {
            field: 'image',
            headerName: 'Image',
            flex: 1,
            renderCell: (params) => (
                params.row.imagePath ? (
                    <Tooltip
                        title={
                            <img
                                src={`/uploads/${params.row.imagePath}`}
                                alt="Gift"
                                style={{ maxWidth: 300, maxHeight: 300, borderRadius: 4 }}
                            />
                        }
                        arrow
                    >
                        <img
                            src={`/uploads/${params.row.imagePath}`}
                            alt="Gift"
                            style={{ maxWidth: 60, maxHeight: 60, borderRadius: 4, cursor: 'pointer' }}
                        />
                    </Tooltip>
                ) : (
                    <span style={{ color: '#999' }}>No image</span>
                )
            ),
        },
        {
            field: 'edit',
            headerName: 'Edit',
            flex: 1,
            renderCell: (params) => (
                <GiftRequestEditCell
                    listController={controller}
                    giftRequest={params.row}
                />
            ),
        },
    ];

    return <Box 
        sx={{
            display: 'flex',
            flexDirection: 'column',
            gap: '1em',
        }}
    >
        <GiftRequestFormDialog controller={giftRequestCreateDialogController} />
        {isMobile ? (
            <Box
                sx={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fill, minmax(260px, 1fr))',
                    gap: 2,
                }}
            >
                {controller.lineItems.map(li => (
                    <Card key={li.giftRequest.id} sx={{ display: 'flex', flexDirection: 'column' }}>
                        {li.giftRequest.imagePath && (
                            <CardMedia
                                component="img"
                                height="140"
                                image={`/uploads/${li.giftRequest.imagePath}`}
                                alt={li.giftRequest.name}
                                sx={{ objectFit: 'cover' }}
                            />
                        )}
                        <CardContent sx={{ flexGrow: 1 }}>
                            <Typography variant="h6">{li.giftRequest.name}</Typography>
                            <Typography variant="body2" color="text.secondary">{li.giftRequest.description}</Typography>
                        </CardContent>
                        <Box sx={{ padding: 2, paddingTop: 0 }}>
                            <GiftRequestEditCell listController={controller} giftRequest={li.giftRequest} />
                        </Box>
                    </Card>
                ))}
            </Box>
        ) : (
            <DataGrid
                rows={controller.lineItems.map(li => li.giftRequest)}
                columns={columns}
                getRowId={(row) =>  row.id}
                slots={{ toolbar: GridToolbar }}
            />
        )}
    </Box>;
});


