import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../../component.loader';
import { observer } from "mobx-react";
import { InvitationDialogController, InviteDialog } from '../Dialog/InvitationDialog';
import { makeObservable, observable } from 'mobx';

registerComponent('invitation-panel', (element, parameters) => { 
    const [invitationList] = parameters;  
    const controller = new InvitationPanelController(invitationList);
    ReactDOMClient.createRoot(element).render(<InvitationPanel controller={controller} />)
});

class InvitationPanelController {
    @observable public invitationList: any;
    constructor(invitationList: any) {
        makeObservable(this);
        this.invitationList = invitationList;
    }
}

const InvitationPanel: React.FC<{controller: InvitationPanelController}> = observer(({controller}) => {
    
    return <>
        <p>Invitation Panel</p>
        {controller.invitationList.map((invitation: any) => (
            <div key={invitation.id}>{invitation.email} - { invitation.isUsed ? 'Consumed' : 'Active'} - {invitation.code}</div>
        ))}
        <InviteDialog controller={new InvitationDialogController()} />
    </>
});