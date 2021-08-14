import Vue from 'vue'
import Vuex from 'vuex'
import {ADD_BOOK, DELETE_BOOK, GET_BOOKS} from "./mutation_types";
import {dataService} from "../shared/data.service";

Vue.use(Vuex)

const state = {
    books: []
}

const mutations = {
    [GET_BOOKS](state,books){
        state.books = books
    },
    [DELETE_BOOK](state,bookId){
        state.books = [...state.books
            .filter(b => b.id !== bookId)]
    },
    [ADD_BOOK](state,book){
        state.books.push(book)
    }
}

const actions = {
    async getBooksAction({commit}) {
        const books = await dataService.getBooks();
        commit(GET_BOOKS,books);
    },
    async addBookAction({commit},book){
        const res = await dataService.addBook(book);
        if(res){
            commit(ADD_BOOK,book);
        }
    },
    async deleteBookAction({commit},bookId){
        await dataService.deleteBook(bookId)
        commit(DELETE_BOOK,bookId)
    }
}

export default new Vuex.Store({
    state,
    mutations,
    actions,
})
