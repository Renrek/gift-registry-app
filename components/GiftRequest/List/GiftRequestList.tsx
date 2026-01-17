import React from "react";
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../../component.loader";
import { observer } from "mobx-react";
import { action, makeObservable, observable } from "mobx";
import { Box } from "@mui/material";
import { DataGrid, GridColDef, GridToolbar } from '@mui/x-data-grid';
import { GiftRequestFormDialogController, GiftRequestFormDialog } from "../FormDialog/GiftRequestFormDialog";
import { GiftRequestDTO } from "../../types";

registerComponent('gift-request-list', (element, parameters) => {
    const [giftRequests, addGiftRequestUrl] = parameters;
    const controller = new GiftRequestListController(giftRequests, addGiftRequestUrl);
    ReactDOMClient.createRoot(element).render(<GiftRequestList controller={controller}/>);
});

class GiftRequestListController {

    @observable
    public giftRequests: Array<GiftRequestDTO> = [];

    public addGiftRequestURL: string;
    constructor(giftRequests: Array<GiftRequestDTO>, addGiftRequestURL: string)
    {
        makeObservable(this);
        this.giftRequests = giftRequests;
        this.addGiftRequestURL = addGiftRequestURL;
    }

    @action
    addGiftRequest(giftRequest: GiftRequestDTO): void
    {
        this.giftRequests = [...this.giftRequests, giftRequest];
    }

    @action
    updateGiftRequest(updatedGiftRequest: GiftRequestDTO): void
    {
        const index = this.giftRequests.findIndex(gr => gr.id === updatedGiftRequest.id);
        if (index !== -1) {
            this.giftRequests[index] = updatedGiftRequest;
        }
    }

    @action
    deleteGiftRequestFromDialog(giftRequest: GiftRequestDTO): void
    {
        this.giftRequests = this.giftRequests.filter(gr => gr.id !== giftRequest.id);
    }

}

const GiftRequestEditCell: React.FC<{ listController: GiftRequestListController; giftRequest: GiftRequestDTO }> = ({ listController, giftRequest }) => {
    const editDialogController = React.useMemo(() => new GiftRequestFormDialogController(
        (result) => listController.updateGiftRequest(result),
        (deletedGift) => listController.deleteGiftRequestFromDialog(deletedGift),
        giftRequest.editPath,
        giftRequest
    ), [giftRequest, listController]);

    return <GiftRequestFormDialog controller={editDialogController} />;
};

const GiftRequestList : React.FC<{
    controller: GiftRequestListController
}> = observer(({controller}) => {

    const giftRequestCreateDialogController = new GiftRequestFormDialogController(
        (result) => {controller.addGiftRequest(result)},
        () => {},
        controller.addGiftRequestURL
    );

    const columns: GridColDef[] = [
        { field: 'name', headerName: 'Name', flex: 1, width: 150 },
        { field: 'description', headerName: 'Description', flex: 3, width: 150 },
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
        <DataGrid
            rows={controller.giftRequests}
            columns={columns}
            getRowId={(row) =>  row.id}
            slots={{ toolbar: GridToolbar }}
        />
    </Box>;
});


