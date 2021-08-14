<template>
    <div>
        <a-row type="flex" justify="space-between" align="middle">
            <label class="ml-3 mb-0 btn btn-primary">
                <input
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                       type="file" id="file"
                       ref="file"
                       v-on:change="handleFileUpload()"/>
                <i class="fa fa-cloud-upload"></i> Upload Excel Sheet
            </label>
            <a-button class="ml-1 btn-primary"
                      :class="!this.viewSheet || !this.validSheet ? 'view_btn' : 'view_btn_visible'"
                      type="normal" :disabled="!this.file"
                      icon="eye" shape="round"
                      @click="()=>this.modalVisible = true">View</a-button>
            <p v-if="message">{{ message }}</p>
            <a-button class="ml-1 btn-primary" type="dashed" :disabled="!this.file" @click="submitFile()">Submit</a-button>
        </a-row>
        <ExcelModal
            :books="sheet"
            :visible="modalVisible"
            :confirm-loading="uploading"
            @submitSheet="submitFile"
            @cancel="()=>this.modalVisible = false"
        />
    </div>
</template>

<script>
import {dataService} from "../../shared/data.service";
import * as XLSX from "xlsx";
import ExcelModal from "./ExcelModal";

export default {
    name: "UploadExcel",
    components: {
        ExcelModal,
    },
    data(){
        return {
            file: '',
            message:'',
            modalVisible:false,
            viewSheet:false,
            uploading:false,
            validSheet:false,
            sheet:[],
        }
    },
    methods: {
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            console.log(
                this.file.type
            )
            this.fileToJson(this.file);
            this.viewSheet = true;
        },
        async submitFile(){
            let data = ''
            if(this.file.type === 'application/vnd.ms-excel'  && this.validSheet){
                const books = [...this.sheet];
                books.forEach(function (r){ delete r.id});
                data = await dataService.patchAddBooks({books})
            }else{
                let formData = new FormData();
                formData.append('file', this.file);
                data = await dataService.patchAddBooksExcel(formData);
            }
            this.$emit('submitted',data.msg);
            this.$refs.file.value=null;
            this.file ='';
            this.modalVisible=false;
            this.viewSheet=false;
        },
        fileToJson (file) {
            const reader = new FileReader()
            reader.onload = (e) => {
                const bstr = e.target.result
                const wb = XLSX.read(bstr, { type: 'binary' })

                const wsname = wb.SheetNames[0]
                const ws = wb.Sheets[wsname]

                const data = XLSX.utils.sheet_to_json(ws, { header: 1 })

                this.sheet = data;
                this.prepareExcelSheetToDisplay()
            }
            reader.readAsBinaryString(file)
        },
        prepareExcelSheetToDisplay(){
            const data = this.sheet;
            if(this.validateSheet(data[0])){
                console.log(this.mapSheetRowsToBooks(data));
                this.sheet = this.mapSheetRowsToBooks(data);
                this.validSheet = true;
            }
            else
                this.validSheet = false;
        },
        validateSheet(headers){
            return headers[0] === 'book title' &&
                headers[1] === 'ISBN' &&
                headers[2] === 'description' &&
                headers[3] === 'author Name'
        },
        mapSheetRowsToBooks(rows){
            return rows.slice(1).map(r => ({
                id: (r[0] || '') + Math.floor(Math.random() * 100),
                title:r[0] || '',
                isbn:r[1] || '',
                description:r[2] || '',
                author:r[3] || '',
            }))
        },

    },
}
</script>

<style scoped>
input[type="file"] {
    display: none;
}
.file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
    margin-left: 40px;
}
.view_btn{
    transition: all 0.3s ease-in-out;
    transform: scaleX(0);
    width: 0;
}
.view_btn_visible{
    transform: scaleX(1);
    visibility: visible;
}
</style>
