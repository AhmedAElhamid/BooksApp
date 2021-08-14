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
                @sheetUploaded="displayExcelSheet"
                @submitted="submittedExcel"
            />
        </a-row>
        <p class="message text-info" v-if="submittedMessage">{{ submittedMessage }}</p>
        <BooksTable
            class="table"
            :books="this.books"
        />
        <BookDrawer
            @close="closeDrawer"
            @addedResource="refreshBooks"
            :visible="drawerVisible"
        />
<!--        <ExcelModal-->
<!--            :books="sheet"-->
<!--            :visible="modalVisible"-->
<!--            :confirm-loading="uploading"-->
<!--            @submitSheet="",-->
<!--            @cancel="()=>this.modalVisible = false"-->
<!--            />-->
<!--        <UploadExcel />-->
    </div>
</template>

<script>
import {dataService} from "../../shared/data.service";
import UploadExcel from "./UploadExcel";
import BooksTable from "./BooksTable";
import BookDrawer from "./BookDrawer";
import ExcelModal from "./ExcelModal";

export default {
    name: "Books",
    components: {ExcelModal, UploadExcel,BooksTable,BookDrawer},
    async created() {
        await this.loadBooks()
    },
    data() {
        return {
            books: [],
            sheet:[],
            submittedMessage:"",
            drawerVisible: false,
            modalVisible:false,
            uploading:false,
        }
    },
    methods: {
        async loadBooks() {
            this.books = await dataService.getBooks()
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
        displayExcelSheet(rows){
            if(this.validateSheet(rows[0])){
                console.log(this.mapSheetRowsToBooks(rows));
                this.sheet = this.mapSheetRowsToBooks(rows);
                this.modalVisible = true;
            }
        },
        mapSheetRowsToBooks(rows){
            return rows.slice(1).map(r => ({
                title:r[0] || '',
                isbn:r[1] || '',
                description:r[2] || '',
                author:r[3] || '',
            }))
        },
        validateSheet(headers){
            return headers[0] === 'book title' &&
                headers[1] === 'ISBN' &&
                headers[2] === 'description' &&
                headers[3] === 'author Name'
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
