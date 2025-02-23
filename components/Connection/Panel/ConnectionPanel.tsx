import React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../../component.loader';
import { action, makeObservable, observable, runInAction } from 'mobx';
import { ConfirmStatus, ConnectionPanelConfig, ConnectionPanelItemDTO } from '../../types';
import { DataGrid, GridColDef, GridDensity } from '@mui/x-data-grid';
import { Button } from '@mui/material';
import { ConnectionFormDialog, ConnectionFormDialogController } from '../FormDialog/ConnectionFormDialog';
import axios from 'axios';
import Notification from '../../utils/notification';

registerComponent('connection-panel', (element, parameters) => {
    const [ config ] = parameters;
    
    const formController = new ConnectionFormDialogController(config.searchUrl, config.addUrl);
    const controller = new ConnectionPanelController(config, formController);
    ReactDOMClient.createRoot(element).render(
        <ConnectionPanel controller={controller} />
    );  
});

class ConnectionPanelController {

    @observable
    public connectedUsers: ConnectionPanelItemDTO[];

    public formController: ConnectionFormDialogController;

    constructor(
        config: ConnectionPanelConfig,
        formController: ConnectionFormDialogController
    ) {
        makeObservable(this);
        this.connectedUsers = config.connectedUsers;
        this.formController = formController;
        
    }

    @action
    public addConnection(confirmUrl: string, id: number) {
        axios.post(confirmUrl).then(() => {
            Notification.success('Connection confirmed');
            const userIndex = this.connectedUsers.findIndex(user => user.id === id);
            if (userIndex !== -1) {
                this.connectedUsers[userIndex].status = ConfirmStatus.CONFIRMED;
            }
        }).catch((err) => {
            Notification.error('Error confirming connection');

        });
        
    }
}

const ConnectionPanel: React.FC<{controller: ConnectionPanelController}> = ({
    controller
}) => {

    const columns: GridColDef[] = [
        { field: 'id', headerName: 'ID', width: 200 },
        { field: 'email', headerName: 'Contact', width: 200 },
        { field: 'manage', headerName: 'Manage', width: 200 },
        { field: 'view', headerName: 'View', width: 200 },
    ];

    columns[2].renderCell = (params) => {
        if (params.row.status === ConfirmStatus.CONFIRMED) {
            return <p>Confirmed</p>
        } else if (params.row.status === ConfirmStatus.PENDING) {
            return <p>Pending</p>
        } else {
            return <Button 
                variant="contained" 
                color="primary"
                onClick={() => { controller.addConnection(params.row.confirmUrl, params.row.id) } }
            >
                Confirm
            </Button>
        }
    };

    columns[3].renderCell = (params) => {
        return <Button variant="contained" color="secondary">View</Button>
    };

    return <>
        <ConnectionFormDialog controller={controller.formController} />
        <DataGrid 
            rows={controller.connectedUsers}
            columns={columns}
            getRowId={(row) => row.id}
            initialState={{
                density: 'compact' as GridDensity,
                sorting: {
                    sortModel: [{ field: 'isUsed', sort: 'asc' }],
                },
                
            }}
        />
    </>
};
