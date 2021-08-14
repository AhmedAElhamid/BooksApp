<template>
    <div>
        <a-row type="flex" justify="center" align="middle">
            <h1>Authors</h1>
        </a-row>
        <AuthorsTable
            :authors="authors"
        />
    </div>
</template>

<script>
import AuthorsTable from "./AuthorsTable";
import {dataService} from "../../shared/data.service";
export default {
    name: "Authors",
    components: {AuthorsTable},
    data() {
        return {
            authors: []
        }
    },
    async created() {
        await this.loadAuthors()
    },
    methods: {
        async loadAuthors() {
            this.authors = await dataService.getAuthors()
        }
    },
}
</script>

<style scoped>

</style>
