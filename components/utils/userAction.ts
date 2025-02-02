class UserAction {
    static async confirm(message: string): Promise<boolean> {
        return new Promise((resolve) => {
            const confirmDialog = document.createElement('div');
            confirmDialog.className = 'confirm-dialog';

            const messageElement = document.createElement('p');
            messageElement.innerText = message;
            confirmDialog.appendChild(messageElement);

            const buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'confirm-buttons-container';

            const yesButton = document.createElement('button');
            yesButton.innerText = 'Yes';
            yesButton.className = 'confirm-yes';
            yesButton.onclick = () => {
                confirmDialog.remove();
                resolve(true);
            };

            const noButton = document.createElement('button');
            noButton.innerText = 'No';
            noButton.className = 'confirm-no';
            noButton.onclick = () => {
                confirmDialog.remove();
                resolve(false);
            };

            buttonsContainer.appendChild(yesButton);
            buttonsContainer.appendChild(noButton);
            confirmDialog.appendChild(buttonsContainer);

            document.body.appendChild(confirmDialog);
        });
    }
}

export default UserAction;

// Attach ConfirmAction to the global window object
if (typeof window !== 'undefined') {
    (window as any).ConfirmAction = UserAction;
}