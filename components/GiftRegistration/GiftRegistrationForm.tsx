import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../component.loader';
import { observer } from 'mobx-react';
import { Box, Button, FormControl, Input, InputLabel } from '@mui/material';
import { action, observable } from 'mobx';
import axios from 'axios';


registerComponent('gift-request-form', (element, parameters) => {
    const controller = new GiftRegistrationController();
    ReactDOMClient.createRoot(element).render(
        <GiftRegistrationForm controller={controller} />
    );  
});

class GiftRegistrationController {

    @observable public name: string = '';
    @observable public description: string = '';

    @action
    public updateName = (name: string): void => {
        this.name = name;
    }

    @action
    public updateDescription = (description: string): void => {
        this.description = description;
    }

    @action
    public submit = (): void => {
        axios.post('/gift-request/add', {
            name: this.name,
            description: this.description,
        }).then((res) => {
            console.log(res);
            
            window.location.reload();
        });
    }
}

const GiftRegistrationForm : React.FC<{
    controller: GiftRegistrationController
}> = observer(({controller}) => {
    return <Box style={{display:'flex', flexDirection:'column', gap:'1em'}}>
        <FormControl>
            <InputLabel htmlFor="name">Name</InputLabel>
            <Input
                id="name"
                type="text"
                onChange={(e) => controller.updateName(e.target.value)}
            />
        </FormControl>
        <FormControl>
            <InputLabel htmlFor="description">Description</InputLabel>
            <Input
                id="description"
                type="text"
                onChange={(e) => controller.updateDescription(e.target.value)}
            />
        </FormControl>
        <Button
            variant='contained'
            onClick={() => controller.submit()}
        >
            Add Gift Request
        </Button>
    </Box>;
});