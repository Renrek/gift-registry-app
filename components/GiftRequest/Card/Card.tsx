import React from "react";
import { observer } from "mobx-react";
import { Button, Card, CardActions, CardContent, Typography } from "@mui/material";
import { GiftRequestDTO } from "../../types";


//Incomplete stub
const GiftRequestCard : React.FC<{
    giftRequest: GiftRequestDTO,
}> = observer(({giftRequest}) => {
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
            onClick={() => {}}
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