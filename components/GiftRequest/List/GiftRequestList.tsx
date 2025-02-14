import React from "react";
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../../component.loader";
import { observer } from "mobx-react";
import { action, makeObservable, observable } from "mobx";
import { Box, Button } from "@mui/material";
import { DataGrid, GridColDef, GridToolbar } from '@mui/x-data-grid';
import axios from "axios";
import { GiftRequestFormDialogController, GiftRequestFormDialog } from "../FormDialog/GiftRequestFormDialog";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";
import UserAction from "../../utils/userAction";
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
    deleteGiftRequest(deletePath: string): void
    {
        axios.delete(deletePath).then(() => {
            this.giftRequests = this.giftRequests.filter((giftRequest) => {
                return giftRequest.deletePath !== deletePath
            });
        });
    }

    @action
    addGiftRequest(giftRequest: GiftRequestDTO): void
    {
        this.giftRequests = [...this.giftRequests, giftRequest];
    }
}

const GiftRequestList : React.FC<{
    controller: GiftRequestListController
}> = observer(({controller}) => {

    const giftRequestCreateDialogController = new GiftRequestFormDialogController(
        (result) => {controller.addGiftRequest(result)},
        controller.addGiftRequestURL
    );

    const handleDelete = async (deletePath: string) => {
        if ( await UserAction.confirm('Are you sure you want to delete this gift request?')) {
            controller.deleteGiftRequest(deletePath);
        }
    }

    const columns: GridColDef[] = [
        { field: 'name', headerName: 'Name', flex: 1, width: 150 },
        { field: 'description', headerName: 'Description', flex: 3, width: 150 },
        { field: 'edit', headerName: 'Manage', flex: 1},
    ];

    columns[2].renderCell = (params) => {
        return <div>
            <Button
                variant="outlined"
                href={params.row.editPath}
            >
                <EditIcon />
            </Button>
            <Button 
                variant="contained"
                color="error"
                onClick={() => handleDelete(params.row.deletePath)}
            >
                <DeleteIcon />
            </Button>
        </div>;
    };

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


