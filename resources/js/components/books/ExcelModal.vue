<template>
<!--    <a-modal-->
<!--        title="Title"-->
<!--        :visible="visible"-->
<!--        :confirm-loading="confirmLoading"-->
<!--        @ok="handleOk"-->
<!--        @cancel="handleCancel"-->
<!--    >-->
    <a-modal :visible="visible" title="Basic Modal"
             @ok="handleOk"
             @cancel="handleCancel">
        <BooksTable
            :books="books"
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
