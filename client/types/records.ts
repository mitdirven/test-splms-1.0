import { z } from 'zod'
const { schema } = useRecords();

export type Record = {
    id: number;
    hashid?: string;
    control_number?: number;
    title?: string;
    subject?: string;
    document_type?: {
        id: number;
        hashid: string;
        code: string;
        name: string;
    };
    user?: {
        id: number;
        username: string;
        email: string;
        email_verified_at: string;
        profile: {
            first_name: string;
            middle_name: string;
            last_name: string;
        }
    };
    created_at?: string;
    deleted_at?: string;
    updated_at?: string;
};

export type Schema = z.infer<typeof schema>;