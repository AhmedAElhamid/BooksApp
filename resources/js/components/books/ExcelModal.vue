<template>
    <a-modal :visible="visible" title="View Sheet"
             :dialog-style="{ top: '20px' }"
             @cancel="handleCancel"
             :width="640">
        <p class="text-center text-info">This is the books that will be uploaded</p>
        <template slot="footer">
            <a-button key="back" @click="handleCancel">
                Return
            </a-button>
            <a-button key="submit" type="primary" :loading="confirmLoading" @click="handleOk">
                Submit
            </a-button>
        </template>
        <BooksTable
            :books="books"
            :allowDelete="false"
            />
    </a-modal>
</template>

<script>
import BooksTable from "./BooksTable";
export default {
    name: "ExcelModal",
    components: {BooksTable},
    props: {
        books: {
            type: Array,
            default: []
        },
        visible:{
            type: Boolean,
            default: false
        },
        confirmLoading:{
            type: Boolean,
            default: false
        }
    },
    methods: {
        handleOk() {
            this.$emit('submitSheet')
            this.confirmLoading = true;
        },
        handleCancel() {
            this.$emit('cancel')
        },
    },
}
</script>

<style scoped>

</style>
