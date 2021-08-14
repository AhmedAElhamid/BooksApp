<template>
    <div>
        <Navbar />
        <div class="container">
            <br/>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import Navbar from "./components/layout/Navbar";
import Main from "./components/layout/Main";
export default {
    components: {Main, Navbar}
}
</script>
