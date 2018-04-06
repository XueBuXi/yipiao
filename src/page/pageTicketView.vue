<template>
    <section class="tkList">
        <el-header>
             <el-button @click="$router.back()" type="primary" class="btnBack" icon="el-icon-back"></el-button>
            检票
        </el-header>
        <el-alert title="入场时请出示此页面二维码" description="Tips:左右滑动可以切换哦" center :closable="false" type="info" show-icon></el-alert>
        <br>
        <br>
        <el-carousel trigger="click" v-swipe="{methods: swipe, arg: {index:6,item:'swipe'}}" :autoplay="false" type="card" height="400px" ref="mainbox">
            <el-carousel-item v-for="i in tickets" v-if="i.seat!==null" :key="i.ticket" :name="i.ticket">
                <el-card class="box-card" shadow="always">
                    <div slot="header" class="clearfix">
                        <h2 class="chead">{{i.seat?'#'+i.seat:"未选座"}}<sup class="h2badge el-badge__content">{{levels[i.level]||i.level}}</sup></h2>
                    </div>
                    <div>
                        <el-alert v-if="i.status=='use'" center :closable="false" title="已入场使用" type="error" show-icon></el-alert>
                        <el-alert v-if="i.status=='active'" center :closable="false" :title="(i.type=='offline'?'线下':'线上')+'票 / 可用'" type="success" show-icon></el-alert>
                        <qrcode :value="'https://in1z.e123.pw/yipiao/?qr/'+i.ticket+'@'+i.tpass" :options="{ size: 200 }"></qrcode>
                        <span class="ticket-no">{{i.ticket}}@{{i.tpass}}</span>
                    </div>
                </el-card>
            </el-carousel-item>
        </el-carousel>
        
    </section>
</template>

<script>
import ajax from 'djax';
export default { 
    data:function(){
        return {
            loading:false,
            levels:{"normal":"普通"},
            qr:"123456",
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
        swipe:function(e){
            console.log(e.direction);
            if(e.direction=="Left")this.$refs.mainbox.next();
            if(e.direction=="Right")this.$refs.mainbox.prev();
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
        that.$nextTick(function(){
            that.$refs.mainbox.setActiveItem(that.$route.params.id);
        })
    })
}
</script>

<style>
.chead{
    margin:0;
}
.el-carousel__item {
    min-width: 70%;
    margin-left: -5%;
    text-align: center;
}
.is-active.el-carousel__item--card.is-in-stage .el-card{
    opacity: 1;
}
.el-carousel__item--card .el-card{
    opacity: 0.2;
}
.ticket-no {
    color: #aaa;
    font-size: 12px;
    display: block;
    position: relative;
    top: -15px;
    font-family: Consolas, Monaco, monospace;
}
</style>
