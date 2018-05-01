<template>
    <div id="app">
        <el-container>
            <el-main>
                <div>
                    <transition name="slide-fade">
                        <router-view></router-view>
                    </transition>
                    <el-header><i class="el-icon-loading"></i> 加载中</el-header>
                </div>
            </el-main>
        </el-container>
    </div>
</template>

<script>
window.$SVR="https://in1z.e123.pw/yipiao/?";
window.$API=$SVR+"api/";
import ajax from 'djax';
export default {
    methods: {

    },
    created:function(){
        var that=this;
        ajax({
            url:$API+"user/status",
            xhrFields: {withCredentials: true},
        }).done(function(e){
            if(e.url)location.href=e.url;
            else if(that.$route.path=="/")that.$router.push("/home");
        }).fail(function(){

        })
    }
} 
</script>

<style>
.el-message-box.alertBox {
    max-width: 90%;
}
html,body{
    overflow: hidden;
    height:100%;
}
#app {
    font-family: Helvetica, sans-serif;
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
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 2001;
    padding: 0;
    text-align: center;
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
    position: absolute;
    left:0;
    right:0;
    top:0;
    bottom:40px;
}
.el-dialog{
     max-width:90%;margin:0 auto;
}
.slide-fade-enter-active {
  transition: all .2s;
}
.slide-fade-leave-active {
  transition: all .2s ;
}
.slide-fade-leave-to{
  transform: translateX(-100vw);
}
.slide-fade-enter{
  transform: translateX(100vw);
}
main>div {
    position: relative;
}
main>div>section {
    position: absolute;
    overflow: hidden;
    width: 100%;
}
.slide-fade-leave-active .el-header {
    display: none !important;
}
.slide-fade-enter-active .el-header{
    display:none !important;
}
.btnBack {
    float: left;
    border-right: 1px solid #fff;
    border-radius: 0;
    height: 100%;
}
.el-header .btnSubmit{
    float: right;
    border-left: 1px solid #fff;
    border-radius: 0;
    height: 100%;
}
.el-header .btnSLeft{
    float: left;
    border-right: 1px solid #fff;
    border-radius: 0;
    height: 100%;
}
.el-main>div>.el-header {
    z-index: 2000;
}
</style>
