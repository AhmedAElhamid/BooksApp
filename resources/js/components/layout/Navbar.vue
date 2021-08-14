<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse">
            <div class="navbar-nav">
                <router-link to="/" class="nav-item nav-link navbar-brand">BookApp</router-link>
                <router-link :to="{name:'books'}" class="nav-item nav-link">Books</router-link>
                <router-link :to="{name:'authors'}" class="nav-item nav-link">Authors</router-link>
            </div>
        </div>
    </nav>
</template>
