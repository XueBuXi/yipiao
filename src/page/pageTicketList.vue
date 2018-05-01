<template>
    <section class="tkList">
        <el-header>
            <el-button @click="gogoupiao" type="primary" class="btnSLeft" icon="el-icon-goods">购票</el-button>
            我的票
            <el-button @click="$router.push('/ticket/add')" type="primary" class="btnSubmit" icon="el-icon-edit">门票激活</el-button>
        </el-header>
        <div class="empty" v-if="tickets.length==0">
            <br>
            <el-alert title="你还没有票哦" center :closable="false" type="info" show-icon></el-alert>
            <br><el-card class="box-card" style="text-align:center">如已购票 请<a href="javascript:" @click="$router.push('/ticket/add')">点这里激活</a><br/>
            还没有票？请<a @click="gogoupiao" href="javascript:">点击购买</a>
            </el-card>
        </div>
        <el-button style="display:block;margin:0 auto;" @click.native="gozzs" size="small">查看更多活动 | SPARK网络公益——线上支教</el-button>
        <div class="packets item" @click="$router.push({path:'/ticket/'+i.ticket+(i.seat||i.level=='normal'?'':'/seat?level='+i.level)})" v-for="(i,a) in tickets" :key="a+'-'+i.id">
            <img src="../assets/img/ticket.png">
            <div class="center">
                <h2 v-if="i.level=='vip'">{{i.seat?'#'+i.seat:"未选座"}}</h2>
                <h2 v-if="i.level=='normal'">普通票</h2>
                <p class="value">
                    <sup class="el-badge__content">{{levels[i.level]||i.level}}</sup>
                    <span>{{i.seat==null&&i.level!='normal'?"戳我选座":"查看详情 or 入场"}} ></span>
                </p>
                <div class="v-id">{{i.ticket}}</div>
            </div>
        </div>
        
    </section>
</template>

<script>
import ajax from 'djax';
export default { 
    data:function(){
        return {
            loading:false,
            levels:{"normal":"普通"},
            tickets:[
            ]
        }
    },
    methods: {
        gozzs(){window.location.href='http://mp.weixin.qq.com/s/mfi0in5GqMy3PbXvGJVG7A'},
        gogoupiao(){
            app.$confirm("在线支付仅支持购买普通票，<br/>若金额出现几分钱的随机尾数，请照常支付。此功能用于防止购票冲突，给您带来的不便敬请谅解","温馨提示",
            {dangerouslyUseHTMLString:true,customClass:"alertBox",confirmButtonText:"购买普通票"}).then(function(){
                location.href="https://in1z.e123.pw/yipiao/?pay/go/50"
            }) 
        }
    },created () {
        updateData(this);
    },
    beforeRouteUpdate (to, from, next) {
        updateData(this);
    },
}
function updateData(that){
    ajax({
        url:$API+'ticket/byuser',
        xhrFields: {withCredentials: true},
    }).done(function(e){
        that.tickets=e.data;
        //that.tickets=[];
    })
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
