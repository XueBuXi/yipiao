<template>
    <section class="tkList">
        <el-header>
            卡包
        </el-header>
        <div class="packets item" @click="$router.push({path:'/ticket/'+i.ticket+(i.seat?'':'/seat')})" v-for="(i,a) in tickets" :key="a+'-'+i.id">
            <img src="../assets/img/ticket.png">
            <div class="center">
                <h2>{{i.seat?'#'+i.seat:"未选座"}}</h2>
                <p class="value">
                    <sup class="el-badge__content">{{levels[i.level]||i.level}}</sup>
                    <span>{{i.seat==null?"戳我选座":"查看详情 or 入场"}} ></span>
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
                {
                    ticket:"0000000001",
                    seat:null,
                    type:"VIP"
                },{
                    ticket:"0000000002",
                    seat:"1-01",
                    type:"普通"
                }
            ]
        }
    },
    methods: {
        
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
