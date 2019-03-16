<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <a class="navbar-brand" href="#">Visit Group</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" @click="getPaged" href="#">Bläddra</a>
                </li>
                </ul>
                <form class="form-inline mt-2 mt-md-0">
                <input fluid class="form-control mr-sm-2" type="text"  v-model="query" placeholder="Sök produkt" aria-label="Sök">
                <button class="btn btn-outline-success my-2 my-sm-0" @click.prevent="searchProducts" type="submit">Sök</button>
                </form>
            </div>
        </nav>

        <div class="jumbotron">
            <div class="row">
                <div class="col-sm-4" v-for="product in products">
                    <div class="card">
                        <img class="card-img-top" :src="getImage(product.Media)" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">{{ product.Name }}</h5>
                                <p class="card-text">
                                    {{ getInfo('Ingress', product.Information).slice(0, 140) }}...
                                </p>
                                <b-button @click="getModalContent(product)" v-b-modal.modalxl variant="primary">Visa info</b-button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <b-modal ok-only ok-variant="secondary" ok-title="Stäng" bg-variant="dark" text-variant="white" id="modalxl" size="xl" v-if="modalContent" :title="modalContent.Name">
            <div class="image-wrapper">
                <b-img center fluid :src="getImage(modalContent.Media)" alt="Card image cap" />
            </div>

            <div class="block">
                <h3>Beskrivning</h3>
                <div class="description">{{ getInfo('Ingress', modalContent.Information) }}</div>
            </div>

            <div class="block">
                <h3>Övrigt</h3>
                <h3 style="display: inline" v-for="info in modalContent.Information"><b-badge style="margin-right: 1rem;" variant="dark" pill v-show="showInfo(info)">{{info.Name}}</b-badge></h3>
                
            </div>

        </b-modal>

        <div class="text-center" style="height: 100px;">
            <span v-if="(!products && !loading)">Inga produkter hittades</span>
            <b-spinner variant="primary" v-if="loading" label="Text Centered" />
        </div>
    </div>
</template>
<script>

export default {
    
    data() {
        return {
            content: null,
            response: null,
            query: null,
            modalContent: null,
            search: {},
            data: null,
            error: null,
            products: null,
            currentPage: null,
            totalRows: null,
            loading: false,
            perPage: 10,
            continueToken: null,
            currentPage: 0,
            filter: null,
            isSearch: false,
            listElm: document.querySelector('body')
        }
    },
    methods: {
        getModalContent(content) {
            this.modalContent = content;
        },

        call(endpoint, method) {
            
            this.loading = true;
            return axios({
                method: method,
                url: 'api/' + endpoint,
                data: this.filter
            })
            .catch(error => {
                console.log(error);
                this.error = error.response.data.message || error.message;
                this.loading = false;
            });
        },

        getPaged() {
            this.loading = true;
            this.products = {};
            this.filter = {};
            this.isSearch = false;
            this.call('product/getpaged/' + this.perPage, 'get').then(response => {
                
                this.totalRows = response.data.TotalResults;
                this.continueToken = response.data.ContinueToken;
                this.products = response.data.Result;
                this.loading = false;
            });
        },

        getNext() {
            this.call('product/getnext/' + this.continueToken, 'get').then(response => {
                this.totalRows = response.data.TotalResults;
                this.continueToken = response.data.ContinueToken;
                this.products = this.products.concat(response.data.Result);
                this.loading = false;
            });
        },

        searchProducts() {

            this.loading = true;
            this.products = {};
            this.isSearch = true;
            this.currentPage = 0;
            this.filter = {};
            this.filter.Search = this.query;
            this.filter.PageSize = this.perPage;
            this.filter.Page = this.currentPage;
            
            this.call('product', 'post').then(response => {
                console.log(response);
                this.totalRows = response.data.TotalResults;
                this.products = response.data.Result;
                this.loading = false;
            });
        },

        searchNext() {
            this.currentPage++;
            this.filter.Page = this.currentPage;
            this.call('product', 'post').then(response => {
                console.log(response);
                this.products = this.products.concat(response.data.Result);
                this.loading = false;
            });
        },

        getImage(media) {
            let first = {};
            let image = '';
            Object.keys(media).forEach(key => {
                first = media[key];
                image = first.Url;
                return;
            })
            if(!image) {
                image = 'https://upload.wikimedia.org/wikipedia/commons/7/75/No_image_available.png';
            }
            return image;
        },

        showInfo(info) {
            if(info.Value == 'True') {
                return true;
            } 
            return false;
        },

        getInfo(info, items) {
            var result = {}
            let value = '';
            Object.keys(items).forEach(key => {
                let item = items[key];
                if (item.Name == info) {
                    value = item.Value;
                    return;
                }
            })  
            return value;
        },
        scroll () {
            window.onscroll = () => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight;

                if (bottomOfWindow) {
                    if(this.isSearch) {
                        this.searchNext();
                    } else {
                        this.getNext();
                    }
                }
            };
        },


    },
    mounted() {
        // Detect when scrolled to bottom.
        this.getPaged();
        this.scroll();
    }
}

</script>