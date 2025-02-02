import { Button, FormControl, Input, InputLabel } from "@mui/material";
import { action, makeObservable, observable } from "mobx";
import { observer } from "mobx-react";
import React from "react";
import * as ReactDOMClient from 'react-dom/client';
import { registerComponent } from "../../component.loader";
import axios from 'axios';
import { UserDTO } from "../../types";
import Notification from "../../utils/notification";

registerComponent('connection-panel', (element, parameters) => {
    const controller = new ConnectionCreateFormController();
    ReactDOMClient.createRoot(element).render(
        <ConnectionCreateForm controller={controller} />
    );  
});

class ConnectionCreateFormController {

    @observable
    public emailSearchString: string = "";

    @observable
    public userList: UserDTO[] = []

    public constructor() {
        makeObservable(this);
    }

    @action
    public updateEmail = (email: string): void => {
        this.emailSearchString = email;
    }

    @action
    public onSearch = (): void => {
        if(this.emailSearchString.length < 3) {
            Notification.error("Please enter at least 3 characters.");
            return;
        }
        axios.get(`/connection/search/${encodeURIComponent(this.emailSearchString)}`)
        .then((res) => {
            if (res.data.length === 0) {
                Notification.info("No users found.");
                return;
            }
            this.userList = res.data;
        }).catch((err) => {
            Notification.error("An unexpected error occurred.");
        });
    }

    public handleAdd = (user: UserDTO): void => {
        axios.post(`/connection/add`, user).then((res) => {
            Notification.success(`${user.email} will be added to your contacts once they accept.`);
        }).catch((err) => {
            Notification.error("An unexpected error occurred.");
        });
    }

}

const ConnectionCreateForm: React.FC<{
    controller: ConnectionCreateFormController
}> = observer(({controller}) => {
    return <>
        <FormControl>
            <InputLabel htmlFor="email">Email</InputLabel>
            <Input
                id="email"
                type="text"
                value={controller.emailSearchString}
                onChange={(e) => controller.updateEmail(e.target.value)}
            />
        </FormControl>
        <Button 
            onClick={() => {controller.onSearch()}}
        >
            Search
        </Button>

        {controller.userList && controller.userList.map((user: any) => (
            <div>
                {user.email}<Button
                    onClick={() => controller.handleAdd(user)}
                >
                    Add
                </Button>
            </div>
        ))}
    </>
});