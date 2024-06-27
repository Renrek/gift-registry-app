import React from "react";
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../component.loader";
import { observer } from "mobx-react";
import { makeObservable } from "mobx";
import { Box, Button, Card, CardActions, CardContent, Typography } from "@mui/material";

type GiftRequest = {
    id: number,
    name: string,
    description: string,
}

registerComponent('gift-request-list', (element, parameters) => {
    const [giftRequests] = parameters;
    const controller = new GiftRequestController(giftRequests);
    ReactDOMClient.createRoot(element).render(<GiftRequestList controller={controller}/>);
});

class GiftRequestController {

    constructor(public readonly giftRequests: Array<GiftRequest>)
    {
        makeObservable(this);
    }
}

const GiftRequestList : React.FC<{
    controller: GiftRequestController
}> = observer(({controller}) => {
    return <Box 
        sx={{
            display: 'flex',
            flexDirection: 'column',
            gap: '1em',
        }}
    >
        {controller.giftRequests.map((giftRequest) => (
            <GiftRequestCard 
                key={giftRequest.id}
                giftRequest={giftRequest} 
                controller={controller} 
            />
        ))}
    </Box>;
});


const GiftRequestCard : React.FC<{
    giftRequest: GiftRequest,
    controller: GiftRequestController
}> = observer(({giftRequest, controller}) => {
    return <Card
        variant="outlined"
    >
        <CardContent>
            <h3>{giftRequest.name}</h3>
            <p>{giftRequest.description}</p>
        </CardContent>
        <CardActions
            sx={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: 'space-between'
            }}
        >
        <Button
            variant="contained"
            color="error"
        >
            Delete
        </Button>
        <Button
            variant="contained"
        >
            Edit
        </Button>
        </CardActions>
    </Card>;
});