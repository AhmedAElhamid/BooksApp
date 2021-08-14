import {API} from "./config";

const getBooks = async ()=>{
    try{
        const response = await axios.get(`${API}/books`);
        return evaluateResponse(response).books || [];
    }
    catch (e) {
        console.error(e);
        return [];
    }
}

const getBookBYid = async (id)=>{
    try{
        const response = await axios.get(`${API}/books/${id}`);
        return evaluateResponse(response).book || {};
    }
    catch (e) {
        console.error(e);
        return e;
    }
}

const addBook = async (book)=>{
    try{
        const response = await axios.post(`${API}/books`,book);
        return evaluateResponse(response).book || {};
    }
    catch (e) {
        throw e;
    }
}

const updateBook = async (book)=>{
    try{
        const response = await axios.put(`${API}/books/${book.id}`,book);
        return evaluateResponse(response).book || {};
    }
    catch (e) {
        console.error(e);
        return [];
    }
}
const deleteBook = async (id)=>{
    try{
        const response = await axios.delete(`${API}/books/${id}`);
        return evaluateResponse(response) || {};
    }
    catch (e) {
        console.error(e);
        return [];
    }
}
const patchAddBooks = async (books)=>{
    try{
        const response = await axios.post(`${API}/books/multiple`,books);
        return evaluateResponse(response) || {};
    }
    catch (e) {
        console.error(e);
        return [];
    }
}

const patchAddBooksExcel = async (formData)=>{
    try{
        const response = await axios.post(`${API}/books/excel`, formData,{
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return evaluateResponse(response) || {};
    }
    catch (e) {
        console.log(e);
        return {};
    }
}

const checkIfIsbnIsValid = async (isbn)=>{
    try {
        const response = await axios.post(`${API}/books/isbn`,{isbn})
        return evaluateResponse(response) || {};
    }
    catch (e) {
        console.log(e);
        return {};
    }
}

const getAuthors = async ()=>{
    try{
        const response = await axios.get(`${API}/authors`);
        return evaluateResponse(response).authors || [];
    }
    catch (e) {
        console.error(e);
        return [];
    }
}

const evaluateResponse = response =>{
    if(![200,201].includes(response.status)){
        console.log(response);
        throw Error(response.msg);
    }
    return response.data;
}


export const dataService = {
    getBooks,
    getBookBYid,
    addBook,
    patchAddBooks,
    patchAddBooksExcel,
    deleteBook,
    updateBook,
    checkIfIsbnIsValid,
    getAuthors
}
