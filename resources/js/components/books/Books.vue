<template>
    <div>
        <a-row type="flex" justify="center" align="middle">
            <h1>Books</h1>
        </a-row>

        <a-row type="flex" justify="center" align="middle">
            <button class="btn btn-primary"
                @click="addBook"
            >
                + Add
            </button>
            <span class="ml-3">OR</span>
            <UploadExcel
                @submitted="submittedExcel"
            />
        </a-row>
        <p class="message text-info" v-if="submittedMessage">{{ submittedMessage }}</p>
        <BooksTable
            class="table"
            :allowDelete="true"
            :books="books"
            @delete-book="deleteBook"
        />
        <BookDrawer
            @close="closeDrawer"
            @addedResource="refreshBooks"
            :visible="drawerVisible"
        />

    </div>
</template>

<script>
import {dataService} from "../../shared/data.service";
import UploadExcel from "./UploadExcel";
import BooksTable from "./BooksTable";
import BookDrawer from "./BookDrawer";
import ExcelModal from "./ExcelModal";
import {mapActions, mapState} from "vuex";

export default {
    name: "Books",
    components: {ExcelModal, UploadExcel,BooksTable,BookDrawer},
    async created() {
        await this.loadBooks()
    },
    data() {
        return {
            sheet:[],
            submittedMessage:"",
            drawerVisible: false,
        }
    },
    computed:{
        ...mapState(['books'])
    },
    methods: {
        ...mapActions(['getBooksAction','deleteBookAction']),
        async loadBooks() {
            await this.getBooksAction();
        },
        addBook() {
            this.drawerVisible = true;
            console.log("add book")
        },
        closeDrawer(){
            this.drawerVisible = false;
        },
        refreshBooks(){
            this.loadBooks()
        },
        async deleteBook(id){
            console.log(id)
            await this.deleteBookAction(id)
        },
        async submitFile(){
            let formData = new FormData();
            formData.append('file', this.file);
            const data = await dataService.patchAddBooksExcel(formData);
            console.log(data);
            this.file ='';
        },
        submittedExcel(data){
            this.submittedMessage = data
            this.loadBooks()
        }
    },
}
</script>

<style lang="scss" scoped>
.table{
    margin-top: 40px;
}
.message{
    width: 100%;
    text-align: center;
    margin-top: 20px;
}
</style>
