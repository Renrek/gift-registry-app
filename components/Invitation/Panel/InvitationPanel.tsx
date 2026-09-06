import * as React from 'react';
import { registerComponent, renderWithTheme } from '../../component.loader';
import { observer } from "mobx-react";
import { InvitationDialogController, InviteDialog } from '../Dialog/InvitationDialog';
import { makeObservable, observable } from 'mobx';
import { InvitationListItemDTO, InvitationPanelConfig } from '../../types';
import { DataGrid, GridColDef, GridColumnVisibilityModel, GridDensity, GridToolbar } from '@mui/x-data-grid';
import { BooleanChip } from '../../BooleanChip/BooleanChip';
import { Box, Button, Card, CardContent, Typography } from '@mui/material';
import { useIsMobile } from '../../utils/useIsMobile';

registerComponent('invitation-panel', (element, parameters) => {  
    const [ config ] = parameters;
    const controller = new InvitationPanelController(config);
    renderWithTheme(element, <InvitationPanel controller={controller} />)
});

class InvitationPanelController {

    @observable 
    public invitationList: InvitationListItemDTO[];

    public readonly config: InvitationPanelConfig;

    constructor(config : InvitationPanelConfig) {      
        makeObservable(this);
        this.invitationList = config.invitationList;
        this.config = config;
    }
}

const InvitationPanel: React.FC<{controller: InvitationPanelController}> = observer(({controller}) => {
    // const [columnVisibilityModel, setColumnVisibilityModel] = React.useState<GridColumnVisibilityModel>({
    //     code: false
    // });
    
    const invitationList = controller.invitationList;
    const isMobile = useIsMobile();

    const copyCode = (code: string) => {
        if (document.hasFocus()) {
            navigator.clipboard.writeText(code);
        } else {
            window.focus();
            setTimeout(() => {
                navigator.clipboard.writeText(code);
            }, 100);
        }
    };

    const columns: GridColDef[] = [
        { field: 'email', headerName: 'Email', flex: 1 },
        { field: 'code', headerName: 'Code', flex: 2 },
        { field: 'copy', headerName: 'Copy', flex: 1 },
    ];

    // columns[1].renderCell = (params) => {
    //     return <BooleanChip 
    //         value={params.row.isUsed} 
    //         trueConfig={{color: 'success', text: 'Used'}}
    //         falseConfig={{color: 'warning', text: 'Unused'}}
    //     />
    // };

    columns[2].renderCell = (params) => {
        return <Button onClick={() => copyCode(params.row.code)}>Copy Code</Button>
    };

    return <>
        <h4>Invitations</h4>
        <div style={{marginBottom: '1em'}}>
            <InviteDialog controller={new InvitationDialogController(controller.config.createInvitationUrl)} />
        </div>
        {invitationList.length === 0 && <p>No invitations have been currently placed.</p>}
        {invitationList.length > 0 && (isMobile ? (
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
                {invitationList.map(invitation => (
                    <Card key={invitation.id}>
                        <CardContent>
                            <Typography variant="body1">{invitation.email}</Typography>
                            <Typography variant="body2" color="text.secondary" sx={{ wordBreak: 'break-all' }}>{invitation.code}</Typography>
                            <Button onClick={() => copyCode(invitation.code)}>Copy Code</Button>
                        </CardContent>
                    </Card>
                ))}
            </Box>
        ) : (
            <DataGrid
                rows = {controller.invitationList}
                columns={columns}
                getRowId={(row) => row.id}
                // columnVisibilityModel={columnVisibilityModel}
                // onColumnVisibilityModelChange={(newModel) => setColumnVisibilityModel(newModel)}
                initialState={{
                    density: 'compact' as GridDensity,
                }}
            />
        ))}
    </>
});
