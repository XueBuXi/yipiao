<template>
    <section class="tkList">
        <el-header>
            <el-button @click="$router.back()" type="primary" class="btnBack" icon="el-icon-back"></el-button>
            门票激活
        </el-header>
        <el-alert title="只有激活后门票才能检票入场哦" description="Tips:如果无法激活请联系客服~" center :closable="false" type="info" show-icon></el-alert>
        <br>
        <br>
        <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="类型">
                <el-radio-group v-model="form.type" size="mini">
                <el-radio-button label="online">线上票</el-radio-button>
                <el-radio-button label="offline">线下票</el-radio-button>
            </el-radio-group>
            </el-form-item>
            <el-form-item label="票号">
                <el-input v-model="form.ticket"></el-input>
                <span v-if="form.type=='online'">
                    *票号是支付宝订单号后8位
                </span>
                <span v-if="form.type=='offline'">
                    *票号自己找找咯
                </span>
            </el-form-item>
            <el-form-item label="预留手机">
                <el-input v-model="form.phone"></el-input>
            </el-form-item>
            <el-button style="width:100%" @click="submit" type="primary" class="fbtnSubmit" icon="el-icon-check">提交 </el-button>
        </el-form>
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
        submit:function(){
                var that=this;
                ajax({
                    url:$API+'ticket/active/'+this.form.ticket,
                    data:{phone:this.form.phone,type:this.form.type},
                    xhrFields: {withCredentials: true},
                }).done(function(e){
                    that.$message({
                        message: '门票激活成功！',
                        type: 'success',
                        center: true,
                    });
                    that.$router.push("/home");
                }).fail(function(e,f,g){
                    try{
                        var j=JSON.parse(g);
                        that.$alert(j.text||j.msg||"未知错误"+g, '激活失败');
                    }catch(e){
                        that.$alert('是我服务器炸了，还是你网络连不上了？', '网络错误');
                    }
                })
        }
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
</style>
