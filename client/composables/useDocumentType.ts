
import type { DocumentType } from '@/types/document_types';

export const useDocumentTypes = () => {
    const documentTypeData = useState<DocumentType[]>(() => []);

    const documentTypeFetch = async () => {
        const crudable = useCrudable<DocumentType>('document_types', 'document_type');
        const { data } = await crudable.list({});
        documentTypeData.value = data;
    };

    return {
        documentTypeData,
        documentTypeFetch,
    }

}
