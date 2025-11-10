export interface UserProfile {
  id: number;
  email: string;
  fullName?: string;
  roles: string[];
  primaryRole: string;
  token?: string;
}
