export interface ConversationMessage {
  id: number;
  threadId: number;
  senderId: number;
  receiverId: number;
  body: string;
  createdAt: string;
  readAt?: string;
}

export interface NotificationItem {
  id: number;
  userId: number;
  title: string;
  content: string;
  type: string;
  isRead: boolean;
  createdAt: string;
}
