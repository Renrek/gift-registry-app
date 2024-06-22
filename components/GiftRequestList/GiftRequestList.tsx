import React from "react";
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../component.loader";
import { observer } from "mobx-react";

registerComponent('gift-request-list', (element, parameters) => {
    const [giftRequests] = parameters; console.log(giftRequests);
    console.log(giftRequests);
    
    const controller = new GiftRequestController();
    ReactDOMClient.createRoot(element).render(<GiftRequestList />);
});

class GiftRequestController {

}

const GiftRequestList : React.FC<{}> = observer(({}) => {
    return <></>;
});