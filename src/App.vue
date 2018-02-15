<template>
    <div id="app">
        <el-container>
            <el-main>
                <div>
                    <router-view></router-view>
                </div>
            </el-main>
            <el-footer>@2018 XMYZ&Weng</el-footer>
        </el-container>
    </div>
</template>

<script>
export default {
    methods: {

    }
}
</script>

<style>
#app {
    font-family: Helvetica, sans-serif;
    text-align: center;
}
body {
    margin: 0;
}

.el-header {
    height:40px !important;
    color: #fff;
    background: #ff901f;
    line-height: 40px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 2001;
}

footer.el-footer {
    height: 20px !important;
    line-height: 20px;
    font-size: 12px;
    background: #ffe6cc;
    color: #b26e28;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 2001;
}
main.el-main {
    margin-top:35px;
}
</style>
