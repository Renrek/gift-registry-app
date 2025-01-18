import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from '../component.loader';
import { action, makeObservable, observable } from 'mobx';
import { observer } from 'mobx-react';

registerComponent('login-form', (element, parameters) => {   
    const controller = new InviteController();
    ReactDOMClient.createRoot(element).render(<InviteDialog controller={controller} />)
});

class InviteController {

    @observable public email: string = '';

    constructor() {
        makeObservable(this);
    }

    @action
    public updateEmail = (email : string): void => {
        this.email = email;
    }
}

const InviteDialog : React.FC<{controller: InviteController}> = observer(({controller}) => {
    return <></>;
});