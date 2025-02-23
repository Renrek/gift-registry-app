export interface NewConnectionDTO {
    userId: number;
    connectedUserId: number;
}

export interface GiftRequestDTO {
    id: number;
    name: string;
    description: string;
    editPath: string;
    deletePath: string;
}

export interface NewGiftRequestDTO {
    name: string;
    description: string;
}

export interface UserDTO {
    id: number;
    email: string;
}

export interface InvitationListItemDTO {
    id: number;
    email: string;
    isUsed: boolean;
    code: string;
}

export interface InvitationPanelConfig {
    createInvitationUrl: string;
    invitationList: InvitationListItemDTO[];
}

export interface ConnectionPanelConfig {
    searchUrl: string;
    addUrl: string;
    connectedUsers: ConnectionPanelItemDTO[];
}

export interface ConnectionPanelItemDTO {
    id: number;
    userId: number;
    email: string;
    status: ConfirmStatus;
    confirmUrl: string;
    deleteUrl: string;
}

export enum ConfirmStatus {
    CONFIRMED = 'confirmed',
    PENDING = 'pending',
    NOT_CONFIRMED = 'not_confirmed'
}