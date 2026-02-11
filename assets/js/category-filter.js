function categoryFilter(parentCategoryId) {
    const initialOffers = JSON.parse(document.getElementById('category-offers-data').textContent);
    return {
        offers: initialOffers,
        allOffers: initialOffers,
        activeFilter: null,
        loading: false,
        async fetchCategory(categoryId) {
            this.loading = true;
            this.activeFilter = categoryId;
            try {
                const response = await fetch('/wp-json/pivot/category_items/' + categoryId);
                this.offers = await response.json();
            } catch (e) {
                console.error(e);
            }
            this.loading = false;
        },
        showAll() {
            this.offers = this.allOffers;
            this.activeFilter = null;
        }
    };
}
