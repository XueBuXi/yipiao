<template>
    <section class="tkList">
        <el-header>
            <el-button @click="$router.push('/home')" type="primary" class="btnBack" icon="el-icon-back"></el-button>
            购票成功
        </el-header>
        <br>
        <br>
        <div class="succinn">
        <i class="el-icon-success succico"></i>
        <h2>支付成功~</h2>
        <el-button type="primary" size="large" @click="$router.push('/home')" >点击返回门票列表</el-button>
        </div>
    </section>
</template>

<script>
import ajax from 'djax';
export default { 
    data:function(){
        return {
            form:{
                ticket:"",
                phone:"",
                type:"offline"
            }
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
    
}
</script>

<style>
.succinn{text-align:center;}
.succico {
    display: block;
    margin: 0 auto;
    text-align: center;
    font-size: 120px;
    color: green;
}
</style>
