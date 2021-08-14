<template>
    <div>
        <a-row type="flex" justify="space-between" align="middle">
            <label class="ml-3 mb-0 btn btn-primary">
<!--         application/vnd.ms-excel           -->
                <input
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                       type="file" id="file"
                       ref="file"
                       v-on:change="handleFileUpload()"/>
                <i class="fa fa-cloud-upload"></i> Upload (.CSV | .XSLX) Sheet
            </label>
            <a-button class="ml-1 btn-primary" type="dashed" :disabled="!this.file" @click="submitFile()">Submit</a-button>
            <p v-if="message">{{ message }}</p>
        </a-row>
<!--            <p class="text-dark text-info">.CSV,.XSLX</p>-->
    </div>
</template>

<script>
import {dataService} from "../../shared/data.service";
import * as XLSX from "xlsx";

export default {
    name: "UploadExcel",
    data(){
        return {
            file: '',
            message:''
        }
    },
    methods: {
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
            this.fileToJson(this.file);
        },
        async submitFile(){
            let formData = new FormData();
            formData.append('file', this.file);
            const data = await dataService.patchAddBooksExcel(formData);
            console.log(data);
            this.$emit('submitted',data.msg);
            this.$refs.file.value=null;
            this.file ='';
        },
        fileToJson (file) {
            /* Boilerplate to set up FileReader */
            const reader = new FileReader()
            reader.onload = (e) => {
                /* Parse data */
                const bstr = e.target.result
                const wb = XLSX.read(bstr, { type: 'binary' })
                /* Get first worksheet */
                const wsname = wb.SheetNames[0]
                const ws = wb.Sheets[wsname]
                /* Convert array of arrays */
                const data = XLSX.utils.sheet_to_json(ws, { header: 1 })
                /* Update state */
                // this.data = data
                // const header = data.shift()
                this.$emit('sheetUploaded',data);
            }
            reader.readAsBinaryString(file)
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
</style>
