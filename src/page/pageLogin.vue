<template>
    <section class="tkList">
        <el-header>
            壹票Ticket
        </el-header>
        
    </section>
</template>

<script>
export default { 
    data:function(){
        return {
            
        }
    },
    methods: {
        
    },beforeRouteEnter (to, from, next) {
        next(updateData);
    },
    beforeRouteUpdate (to, from, next) {
        updateData(this);
    },
}
function updateData(that){
    that.$router.push("/home");
}
</script>

<style>
.tkList .item {
    position: relative;
}
.tkList img {
    width: 100%;
    display: block;
}
.tkList .center {
    width: 84%;
    position: absolute;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    left: 8%;
}
.tkList .item .value {
    color: rgba(51,51,51,.5);
    font-size: 16px;
    height: 24px;
    line-height: 24px;
}
.tkList .item .v-id{
    font-size: 12px;
    position: relative;
    top: -16px;
    color: #bbb;
}
.tkList .item .value .el-badge__content {
    vertical-align: middle;
}
</style>
