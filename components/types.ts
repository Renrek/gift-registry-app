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