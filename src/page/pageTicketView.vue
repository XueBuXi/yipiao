<template>
    <section class="tkList">
        <el-header>
             <el-button @click="$router.back()" type="primary" class="btnBack" icon="el-icon-back"></el-button>
            检票
            <el-button @click="seatVisible=true" type="primary" class="btnSubmit" icon="el-icon-location">找座位</el-button>
        
        </el-header>
        <el-alert title="入场时请出示此页面二维码" description="Tips:左右滑动可以切换哦" center :closable="false" type="info" show-icon></el-alert>
        <br>
        <br>
        <el-carousel trigger="click" 
        v-swipe="{methods: swipe, arg: {index:6,item:'swipe'}}" :autoplay="false" type="card" height="300px" ref="mainbox">
            <el-carousel-item v-for="i in tickets" v-if="i.level=='normal'||i.seat!=null" :key="i.ticket" :name="i.ticket">
                <el-card class="box-card" shadow="always">
                    <div slot="header" class="clearfix">
                        <h2 class="chead" v-if="i.level=='vip'">{{i.seat?'#'+i.seat:"未选座"}}<sup class="h2badge el-badge__content">{{levels[i.level]||i.level}}</sup></h2>
                        <h2 class="chead" v-if="i.level=='normal'">普通票</h2>
                    </div>
                    <div>
                        <el-alert v-if="i.status=='use'" center :closable="false" title="已入场使用" type="error" show-icon></el-alert>
                        <el-alert v-if="i.status=='active'" center :closable="false" :title="(i.type=='offline'?'线下':'线上')+'票 / 可用'" type="success" show-icon></el-alert>
                        <qrcode :value="'https://in1z.e123.pw/yipiao/?qr/'+i.ticket+'@'+i.tpass" :options="{ size: 128 }"></qrcode>
                        <span class="ticket-no">{{i.ticket}}@{{i.tpass}}</span>
                    </div>
                </el-card>
            </el-carousel-item>
        </el-carousel>
        <!--el-button @click="seatVisible=true" type="primary" class="btnViewSeat" icon="el-icon-location">找座位</el-button-->
        <div class="seatmapdialog diaLogBox el-dialog" v-show="seatVisible">
            <div class="el-dialog__header"><span class="el-dialog__title">座位地图</span>
            </div>
            <div class="">
                <div class="seatmap-box">
                    <div class="seatMap seatYZ" :style="{zoom:appZoom}" v-cloak>
                        <div :key="'line-'+a" class="line" v-for="(i,a) in seatMap" :dkey="(21-a)+'-'">
                            <div :key="'seat-'+a+'-'+(ii==''?(aa+'-empty-'+a):(ii+'-'+aa))"
                            v-for="(ii,aa) in i" 
                            :class="{seat:!isNaN(ii)&&ii!=='',space:ii=='',selected:ii!=''&&selected.indexOf(21-a+'-'+ii)!=-1}"
                            :data-key="(21-a)+'-'+(ii==''?'space':ii)">
                                {{ii==''?'&nbsp;':ii}}
                            </div>
                        </div>
                        <div class="front">
                            <div class="left-wall">墙</div>
                            <div class="front-show">舞台</div>
                            <div class="right-wall">墙</div>
                        </div>
                        <div class="example">
                            <div>Tips:可以左右滑动哦</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ajax from 'djax';
export default { 
    data:function(){
        var that=this;
        try{
        var z=(document.querySelector(".el-main").clientHeight-70)/805;
        }catch(e){
            z=1;
            setTimeout(function(){
                that.appZoom=(document.querySelector(".el-main").clientHeight-70)/805;
            },0);
        }
        return {
            seatVisible:false,
            loading:false,
            appZoom:z,
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
            ],
            selected:[],
            seatMap:[
['','',44,42,40,38,36,34,32,30,28,26,24,22,20,18,'','','','','','','',16,14,12,10,8,6,4,2,1,3,'','','','','','','','','',5,7,9,11,13,15,17,19,21,23,25,27,31,33,35,37,39,41,43,'','','',''],
['',58,56,54,52,50,48,46,44,42,40,38,36,34,32,30,'',28,26,24,22,20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,54,55,57,59,'',''],
['',50,48,46,44,42,40,'','',38,36,34,32,30,28,26,'','',24,22,'',20,18,16,14,12,10,8,6,4,2,1,3,5,'',7,9,11,13,'','',15,17,18,21,23,25,27,'','',29,31,33,35,37,39,41,43,45,47,49,51,'','',''],
[58,56,54,52,50,48,46,44,42,40,38,36,34,32,30,28,'','',26,24,22,20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,54,55,57,'','',''],
[56,54,52,50,48,46,44,42,40,38,36,34,32,30,28,26,'','',24,22,20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,19,'',21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,54,55,57,'','','',''],
['',54,52,50,48,46,44,42,40,38,36,34,32,30,28,26,'','',24,22,20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'','',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,'','','','','',''],
['',52,50,48,46,44,42,40,38,36,34,32,30,28,26,24,'','',22,20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,19,'','',21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,'','','','','','',''],
[],
['',50,48,46,44,42,40,38,36,34,32,30,28,26,24,22,'','',20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'','',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,'','','','','','','','',''],
['',50,48,46,44,42,40,38,36,34,32,30,28,26,24,22,'','',20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,'','',17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,'','','','','','','','','',''],
['','',48,46,44,42,40,38,36,34,32,30,28,26,24,22,'','',20,18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'','',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,'','','','','','','','','',''],
['','','',46,44,42,40,38,36,34,32,30,28,26,24,22,20,'','',18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,17,'','',19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,'','','','','','','','','','',''],
['','','','',44,42,40,38,36,34,32,30,28,26,24,22,20,'','',18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,'','',17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,'','','','','','','','','','','',''],
['','','','','',42,40,38,36,34,32,30,28,26,24,22,20,'','',18,16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,'','',17,19,21,23,25,27,29,31,33,35,37,39,41,43,'','','','','','','','','','','','',''],
['','','','','','',40,38,36,34,32,30,28,26,24,22,20,18,'','',16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,'','',17,19,21,23,25,27,29,31,33,35,37,39,41,'','','','','','','','','','','','','',''],
['','','','','','',40,38,36,34,32,30,28,26,24,22,20,18,'','',16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,15,'','',17,19,21,23,25,27,29,31,33,35,37,39,41,'','','','','','','','','','','','','',''],
['','','','','','','',38,36,34,32,30,28,26,24,22,20,18,'','',16,14,12,10,8,6,4,2,1,3,5,7,9,11,13,'','',15,17,19,21,23,25,27,29,31,33,35,37,39,'','','','','','','','','','','','','','',''],
['','','','','','','',38,36,34,32,30,28,26,24,22,20,18,16,'','',14,12,10,8,6,4,2,1,3,5,7,9,11,13,'','',15,17,19,21,23,25,27,29,31,33,35,37,39,'','','','','','','','','','','','','','',''],
['','','','','','','','','',36,34,32,30,28,26,24,22,20,18,16,'','',12,10,8,6,4,2,1,3,5,7,9,11,13,'','',15,17,19,21,23,25,27,29,31,33,35,37,'','','','','','','','','','','','','','','',''],
['','','','','','','','','',34,32,30,28,26,24,22,20,18,16,14,'','',12,10,8,6,4,2,1,3,5,7,9,11,13,'','',15,17,19,21,23,25,27,29,31,33,35,'','','','','','','','','','','','','','','','',''],
['','','','','','','','','','',32,30,28,26,24,22,20,18,16,14,'','',12,10,8,6,4,2,1,3,5,7,9,11,13,'','',15,17,19,21,23,25,27,29,31,33,'','','','','','','','','','','','','','','','','','']
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
        that.selected=[];
        for(var i in e.data)if(e.data[i].level=='vip')that.selected.push(e.data[i].seat);
        that.$nextTick(function(){
            that.$refs.mainbox.setActiveItem(that.$route.params.id);
        })
    })
}
</script>

<style>
.seatmapdialog .el-dialog__header {
    padding: 0;
    text-align: CENTER;
}
.seatmapdialog button.el-dialog__headerbtn {
    top: -5px;
    right: 0;
    font-size: 30px;
}

.btnViewSeat{
    display:block;
    margin:0 auto;
}
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
    top: -10px;
    font-family: Consolas, Monaco, monospace;
}



.diaLogBox{
    width:400px;
    max-width: 100%;
    margin: 0 auto;
}
.space,.seat {
    display: inline-block;
    width: 30px;
    height: 30px;
    box-sizing: border-box;
    border: 1px solid #eee;
    margin: 0 5px;
    line-height:29px;
}
.seat{
    cursor:pointer;
    background: #ffb743;
    border:1px solid #ff9900;
    color: #fff;
    transition: all 0.2s;
}
.seat:hover{
    background:#ff9900;
}
.line {
    white-space: nowrap;
    /* width: 100%; */
    height: 35px;
    /* display: flex; */
}

.seatMap {
    position: relative;
    text-align: center;
    width: 2600px;
}
.space{
    border:0;
}
.seatA{
    background: #ff7272;
    color: #fff;
    border: 1px solid #ff7272;
}
.seatA:hover{
    background:#c44646;
}
.seat.booked {
    background: #aaa !important;
    border-color: #000;
    cursor: not-allowed;
}
.seat.selected{
    background: #47a547 !important;
    border-color: #008000;
}
.seatMap[v-cloak]{
    display:none;
}
.seatMap{}
.seatmap-box {
    width: 100%;
    overflow: auto;
}
.front div {
    display: inline-block;
    height: 60px;
    line-height: 60px;
    background: #eee;
    color: #999;
    font-size: 30px;
    text-align: center;
    margin-bottom: 10px;
}

.front {
    text-align: left;
}

.left-wall {
    width: 740px;
    margin-left: 0px;
}

.front-show {
    margin-left: 10px;
    width: 790px;
}

.right-wall {
    margin-left: 10px;
    width: 900PX;
}
.example {
    position: absolute;
    left: 30px;
    bottom: 90px;
    text-align: left;
}
.example .seat{
    text-align: center;
}
.el-message {
    margin-top: 30px;
} 
.diaLogBox.el-dialog {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    right: 0;
    bottom: 0;
}
</style>
