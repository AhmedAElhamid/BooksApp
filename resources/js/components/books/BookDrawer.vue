<template>
    <a-drawer
        title="Add a new book"
        :width="520"
        :visible="visible"
        :body-style="{ paddingBottom: '80px' }"
        @close="onClose"
    >
        <a-form :form="form" layout="vertical" hide-required-mark  @submit="handleSubmit">
            <a-form-item label="Title">
                <a-input
                    v-decorator="['title', { rules: [{ required: true, message: 'Please input book title!' }] }]"
                />
            </a-form-item>
            <a-row type="flex" justify="space-between" align="middle">
                <a-col span="8">
                    <a-form-item
                        :has-feedback="isbnStatus === 'validating'"
                        :validate-status="isbnStatus"
                        help="ISBN should be unique"

                        label="ISBN">
                        <a-input-number
                            @change="onISBNChange"
                            v-decorator="['isbn', { rules: [{ required: true, message: 'Please input ISBN!' }] }]"
                        />
                    </a-form-item>
                </a-col>
                <a-col span="16">
                    <p class="text-danger mt-3" v-if="isbnStatus === 'error'"> ISBN is already taken</p>
                </a-col>
            </a-row>
            <a-form-item label="Author">
                <a-input
                    v-decorator="['author', { rules: [{ required: true, message: 'Please input the author name!' }] }]"
                />
            </a-form-item>
            <a-form-item label="Description">
                <a-textarea
                    v-decorator="['description', { rules: [{ required: true, message: 'Please input the author name!' }] }]"
                    placeholder="description"
                    auto-size />
            </a-form-item>

            <p class="text-danger" v-if="errorMessage">{{ errorMessage }}</p>
            <div
                class="footer"
            >
                <a-button class="cancel-btn"
                          @click="onClose">
                    Cancel
                </a-button>
                <a-button type="primary" html-type="submit">
                    Submit
                </a-button>
            </div>
        </a-form>
    </a-drawer>
</template>

<script>
import {dataService} from '../../shared/data.service'
const isValid = 'success'
const isNotValid = 'error'
const validating = 'validating'
export default {
    name: "BookDrawer",
    props: {
        visible: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            form: this.$form.createForm(this,{ name: 'book' }),
            isbnStatus:'',
            errorMessage: '',
        };
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault();
            this.form.validateFields(async (err, values) => {
                if (!err) {
                    try {
                        await dataService.addBook(values);
                        this.$emit('addedResource');
                        this.form.resetFields();
                        this.onClose();
                    }catch (e) {
                        this.errorMessage = e.response?.data?.errors
                            ? Object.values(e.response?.data?.errors)[0][0]
                            : e.message;
                    }
                }
            });
        },
        onClose() {
            this.$emit('close');
        },
        async onISBNChange(e){
            if(!e)
                this.isbnStatus = ''
            else{
                this.isbnStatus = validating;
                const response = await dataService.checkIfIsbnIsValid(e);
                this.isbnStatus = response.success
                    ? isValid
                    : isNotValid;
            }
        }
    },
}
</script>

<style lang="scss" scoped>
.footer{
    position: absolute;
    right: 0;
    bottom: 0;
    width: 100%;
    border-top: 1px solid #e9e9e9;
    padding: 10px 16px;
    background: #fff;
    text-align: right;
    z-index: 1;

    .cancel-btn{
        margin-right: 8px;
    }
}
</style>
