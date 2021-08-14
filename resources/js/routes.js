import Books from "./components/books/Books";
import Authors from "./components/authors/Authors";
import PageNotFound from "./components/shared/page-not-found";


export const routes = [
    {
      path: '/',
      redirect: 'books'
    },
    {
        name: 'books',
        path: '/books',
        component: Books
    },
    {
        name: 'authors',
        path: '/authors',
        component: Authors
    },
    {
        name: 'page-not-found',
        path: '*',
        component: PageNotFound
    }
];
