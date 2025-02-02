class Notification {
    static createNotification(type: 'success' | 'error' | 'info', message: string): void {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerText = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    static success(message: string): void {
        this.createNotification('success', message);
    }

    static error(message: string): void {
        this.createNotification('error', message);
    }

    static info(message: string): void {
        this.createNotification('info', message);
    }
}

export default Notification;

// Attach Notification to the global window object
if (typeof window !== 'undefined') {
    (window as any).Notification = Notification;
}