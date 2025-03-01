import React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../../component.loader';
import { observer } from 'mobx-react';
import { makeObservable } from 'mobx';
import { GiftSelectionPanelConfig, GiftSelectionPanelItemDTO } from '../../types';
import { DataGrid, GridColDef, GridToolbar } from '@mui/x-data-grid';
import { Button } from '@mui/material';

registerComponent('gift-selection-panel', (element, parameters) => {
    const [ config ] = parameters;
    const controller = new GiftSelectionPanelController(config);
    ReactDOMClient.createRoot(element).render(
        <GiftSelectionPanel controller={controller}/>
    );  
});

class GiftSelectionPanelController {

    public gifts: GiftSelectionPanelItemDTO[];

    constructor(
        config: GiftSelectionPanelConfig
    ) {
        makeObservable(this);
        this.gifts = config.gifts; console.log('gifts', this.gifts);
        
    }
}

const GiftSelectionPanel: React.FC<{
    controller: GiftSelectionPanelController
}> = observer(({controller}) => {

    const columns: GridColDef[] = [
        { field: 'giftId', headerName: 'ID', flex: 1 },
        { field: 'name', headerName: 'Name', flex: 1 },
        { field: 'description', headerName: 'Description', flex: 1 },
        { field: 'view', 
            headerName: 'View', 
            flex: 1, 
            renderCell: (params) => {
                return <Button 
                    onClick={() => {
                    
                    }}
                >
                    View
                </Button>;
            }
        },
    ];

    return <>
        <div>
            Gift Selection Panel
        </div>
        {controller.gifts && <DataGrid 
            rows={controller.gifts} 
            columns={columns}
            getRowId={(row) => row.giftId}
            slots={{ toolbar: GridToolbar}}
        />}
        
    </>;
});