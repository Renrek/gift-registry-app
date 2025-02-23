import { Button, FormControl, Input, InputLabel } from "@mui/material";
import { action, makeObservable, observable } from "mobx";
import { observer } from "mobx-react";
import React from "react";
import axios from 'axios';
import { UserDTO } from "../../types";
import Notification from "../../utils/notification";


export class ConnectionCreateFormController {

    @observable
    public emailSearchString: string = "";

    @observable
    public userList: UserDTO[] = []

    public constructor(
        public searchURL: string,
        public addURL: string
    ) {
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
        axios.get(this.searchURL, {
            params: {
                emailPartial: this.emailSearchString
            }
        })
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

    @action
    public handleAdd = (user: UserDTO): void => {
        axios.post(this.addURL, user).then((res) => {
            Notification.success(`${user.email} will be added to your contacts once they accept.`);
        }).catch((err) => {
            Notification.error("An unexpected error occurred.");
        });
    }

}

export const ConnectionCreateForm: React.FC<{
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