import React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../../component.loader';
import { action, makeObservable, observable, runInAction } from 'mobx';
import { ConfirmStatus, ConnectionPanelConfig, ConnectionPanelItemDTO } from '../../types';
import { DataGrid, GridColDef, GridDensity, GridToolbar } from '@mui/x-data-grid';
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
        { field: 'email', headerName: 'Contact', flex: 1 },
        { field: 'manage', headerName: 'Manage', flex: 1 },
    ];

    columns[1].renderCell = (params) => {
        if (params.row.status === ConfirmStatus.CONFIRMED) {
            return <Button href={params.row.viewUrl} >View</Button>
        } else if (params.row.status === ConfirmStatus.PENDING) {
            return <p>Pending</p>
        } else {
            return <Button 
                variant="outlined"
                size="small" 
                color="primary"
                onClick={() => { controller.addConnection(params.row.confirmUrl, params.row.id) } }
            >
                Confirm
            </Button>
        }
    };

    
    return <>
        <h4>Contacts</h4>
        <div style={{marginBottom: '1em'}}>
        <ConnectionFormDialog controller={controller.formController} />
        </div>
        {controller.connectedUsers.length === 0 && <p>No Contacts Found.</p>}
        {controller.connectedUsers.length > 0 && <DataGrid
            rows = {controller.connectedUsers}
            columns={columns}
            getRowId={(row) => row.id}
            slots={{ toolbar: GridToolbar}}
            initialState={{
                density: 'compact' as GridDensity,
                sorting: {
                    sortModel: [{ field: 'isUsed', sort: 'asc' }],
                },
                
            }}
        />}
    </>
};
