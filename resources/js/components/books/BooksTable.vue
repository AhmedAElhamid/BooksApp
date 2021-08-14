<template>
    <a-table bordered :data-source="books | sortBooks | mapBooksToTableData" :columns="columns">
        <div
            slot="filterDropdown"
            slot-scope="{ setSelectedKeys, selectedKeys, confirm, clearFilters, column }"
            style="padding: 8px"
        >
            <a-input
                v-ant-ref="c => (searchInput = c)"
                :placeholder="`Search ${column.dataIndex}`"
                :value="selectedKeys[0]"
                style="width: 188px; margin-bottom: 8px; display: block;"
                @change="e => setSelectedKeys(e.target.value ? [e.target.value] : [])"
                @pressEnter="() => handleSearch(selectedKeys, confirm, column.dataIndex)"
            />
            <a-button
                type="primary"
                icon="search"
                size="small"
                style="width: 90px; margin-right: 8px"
                @click="() => handleSearch(selectedKeys, confirm, column.dataIndex)"
            >
                Search
            </a-button>
            <a-button size="small" style="width: 90px" @click="() => handleReset(clearFilters)">
                Reset
            </a-button>
        </div>
        <a-icon
            slot="filterIcon"
            slot-scope="filtered"
            type="search"
            :style="{ color: filtered ? '#108ee9' : undefined }"
        />
        <template slot="customRender" slot-scope="text, record, index, column">
            <span v-if="searchText && searchedColumn === column.dataIndex">
            <template
                v-for="(fragment, i) in text
                .toString()
                .split(new RegExp(`(?<=${searchText})|(?=${searchText})`, 'i'))"
            >
              <mark
                  v-if="fragment.toLowerCase() === searchText.toLowerCase()"
                  :key="i"
                  class="highlight"
              >{{ fragment }}</mark
              >
              <template v-else>{{ fragment }}</template>
        </template>
      </span>
            <template v-else>
                {{ text }}
            </template>
        </template>

        <template slot="operation" slot-scope="text, record">
            <a-popconfirm
                v-if="books.length"
                title="Sure to delete?"
                @confirm="() => onDelete(record.key)"
            >
                <a href="delete">Delete</a>
            </a-popconfirm>
        </template>
    </a-table>
</template>

<script>
export default {
    name:"BooksTable",
    props: {
        allowDelete: {
            type: Boolean,
            default: false
        },
        books:{
            type: Array,
            default: []
        },
    },
    data() {
        return {
            searchText: '',
            searchInput: null,
            searchedColumn: '',
            columns: [
                {
                    title: 'Title',
                    dataIndex: 'title',
                    key: 'title',
                    scopedSlots: {
                        filterDropdown: 'filterDropdown',
                        filterIcon: 'filterIcon',
                        customRender: 'customRender',
                    },
                    onFilter: (value, record) =>
                        record.title
                            .toString()
                            .toLowerCase()
                            .includes(value.toLowerCase()),
                    onFilterDropdownVisibleChange: visible => {
                        if (visible) {
                            setTimeout(() => {
                                this.searchInput.focus();
                            }, 0);
                        }
                    },
                },
                {
                    title: 'ISBN',
                    dataIndex: 'isbn',
                    key: 'isbn',
                    scopedSlots: {
                        filterDropdown: 'filterDropdown',
                        filterIcon: 'filterIcon',
                        customRender: 'customRender',
                    },
                    onFilter: (value, record) =>
                        record.isbn
                            .toString()
                            .toLowerCase()
                            .includes(value.toLowerCase()),
                    onFilterDropdownVisibleChange: visible => {
                        if (visible) {
                            setTimeout(() => {
                                this.searchInput.focus();
                            });
                        }
                    },
                },
                {
                    title: 'Author Name',
                    dataIndex: 'author',
                    key: 'author',
                    scopedSlots: {
                        filterDropdown: 'filterDropdown',
                        filterIcon: 'filterIcon',
                        customRender: 'customRender',
                    },
                    onFilter: (value, record) =>
                        record.author
                            .toString()
                            .toLowerCase()
                            .includes(value.toLowerCase()),
                    onFilterDropdownVisibleChange: visible => {
                        if (visible) {
                            setTimeout(() => {
                                this.searchInput.focus();
                            });
                        }
                    },
                },
                {
                    title: 'Description',
                    dataIndex: 'description',
                    key: 'description',
                    ellipsis: true,
                    scopedSlots: {
                        filterDropdown: 'filterDropdown',
                        filterIcon: 'filterIcon',
                        customRender: 'customRender',
                    },
                    onFilter: (value, record) =>
                        record.description
                            .toString()
                            .toLowerCase()
                            .includes(value.toLowerCase()),
                    onFilterDropdownVisibleChange: visible => {
                        if (visible) {
                            setTimeout(() => {
                                this.searchInput.focus();
                            }, 0);
                        }
                    },
                },
            ],
        };
    },
    created() {
        if(this.allowDelete)
            this.columns.push({
                title: 'Actions',
                dataIndex: 'operation',
                scopedSlots: { customRender : 'operation' },
            })
    },
    methods: {
        handleSearch(selectedKeys, confirm, dataIndex) {
            confirm();
            this.searchText = selectedKeys[0];
            this.searchedColumn = dataIndex;
        },
        handleReset(clearFilters) {
            clearFilters();
            this.searchText = '';
        },
        onDelete(key) {
            this.$emit('delete-book',key)
        },
    },
    filters: {
        mapBooksToTableData: function (books){
            return books.map(({id,title,description,isbn,author}) => ({
                key: id,
                title,
                description,
                isbn,
                author:author.name || author,
            }))
        },
        sortBooks: function (books){
            return [...books]
                .sort((a,b)=>
                    new Date(b['created_at']) - new Date(a['created_at']))
        }
    },
};
</script>
<style scoped>
.highlight {
    background-color: rgb(255, 192, 105);
    padding: 0px;
}
</style>
