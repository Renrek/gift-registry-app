import React from 'react';
import { registerComponent, renderWithTheme } from '../../component.loader';
import { action, makeObservable, observable, runInAction } from 'mobx';
import { ConfirmStatus, ConnectionPanelConfig, ConnectionPanelItemDTO } from '../../types';
import { DataGrid, GridColDef, GridDensity, GridToolbar } from '@mui/x-data-grid';
import { Box, Button, Card, CardContent, Typography } from '@mui/material';
import { ConnectionFormDialog, ConnectionFormDialogController } from '../FormDialog/ConnectionFormDialog';
import axios from 'axios';
import Notification from '../../utils/notification';
import { useIsMobile } from '../../utils/useIsMobile';

registerComponent('connection-panel', (element, parameters) => {
    const [ config ] = parameters;
    
    const formController = new ConnectionFormDialogController(config.searchUrl, config.addUrl);
    const controller = new ConnectionPanelController(config, formController);
    renderWithTheme(element, <ConnectionPanel controller={controller} />);
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

    const isMobile = useIsMobile();

    const renderManageAction = (row: ConnectionPanelItemDTO) => {
        if (row.status === ConfirmStatus.CONFIRMED) {
            return <Button href={row.viewUrl} >View</Button>
        } else if (row.status === ConfirmStatus.PENDING) {
            return <p>Pending</p>
        } else {
            return <Button 
                variant="outlined"
                size="small" 
                color="primary"
                onClick={() => { controller.addConnection(row.confirmUrl, row.id) } }
            >
                Confirm
            </Button>
        }
    };

    const columns: GridColDef[] = [
        { field: 'email', headerName: 'Contact', flex: 1 },
        { field: 'manage', headerName: 'Manage', flex: 1 },
    ];

    columns[1].renderCell = (params) => renderManageAction(params.row);

    
    return <>
        <h4>Contacts</h4>
        <div style={{marginBottom: '1em'}}>
        <ConnectionFormDialog controller={controller.formController} />
        </div>
        {controller.connectedUsers.length === 0 && <p>No Contacts Found.</p>}
        {controller.connectedUsers.length > 0 && (isMobile ? (
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
                {controller.connectedUsers.map(user => (
                    <Card key={user.id}>
                        <CardContent sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 1 }}>
                            <Typography variant="body1">{user.email}</Typography>
                            {renderManageAction(user)}
                        </CardContent>
                    </Card>
                ))}
            </Box>
        ) : (
            <DataGrid
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
            />
        ))}
    </>
};
