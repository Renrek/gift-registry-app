import * as React from 'react';
import * as ReactDOMClient from 'react-dom/client';
import './LoginForm.scss';
import { observer } from 'mobx-react';
import { registerComponent } from '../component.loader';

registerComponent('login', (element, parameters) => {
    ReactDOMClient.createRoot(element).render(<LoginForm />)
});

const LoginForm : React.FC<{}> = observer(({}) => {
    return <>
        <button
            onClick={() => alert('Cool')}
        >Hi</button>
    </>
});