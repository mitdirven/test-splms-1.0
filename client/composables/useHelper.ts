
export const useHelper = () => {
    const isLoading = ref(false);
    const router = useRouter();
    const route = useRoute();

    // Toggle Loading
    const toggleLoading = () => {
        isLoading.value = !isLoading.value;
      };

    const formatDate = (dateString: string) => {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('en-PH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };
    

    return {
        isLoading,
        formatDate,
        router,
        route,
        toggleLoading
    }
}
