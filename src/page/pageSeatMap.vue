<template>
    <section v-loading="loading">
        <el-header>
            <el-button @click="$router.back()" type="primary" class="btnBack" icon="el-icon-back"></el-button>
            选座
            <el-button :disabled="dialogVisible||selected.length<1" @click="submitSeat" type="primary" class="btnSubmit" icon="el-icon-check">提交 </el-button>
        </el-header>
        <div class="diaLogBox el-dialog" v-if="dialogVisible">
            <div class="el-dialog__header"><span class="el-dialog__title">选座指南</span></div>
            <div class="el-dialog__body">
                <ol>
                <li>系统暂不支持为多张票同时选座</li>
                <li>选座并确认后不可更改</li>
                <li>只允许选取票种对应的座位</li>
                <li>座位图可以左右滑动</li>
                <li>再次点击已选座位可以取消</li>
            </ol>
            </div>
            <div class="el-dialog__footer">
                <span class="dialog-footer">
                    <el-button type="primary" @click="dialogVisible = false">我明白了，开始选座</el-button>
                </span>
            </div>
        </div>
        <div class="seatmap-box" v-show="!dialogVisible">
            <div class="seatMap seatYZ" :style="{zoom:appZoom}" v-cloak>
                <div :key="'line-'+a" class="line" v-for="(i,a) in seatMap" :dkey="(21-a)+'-'">
                    <div :key="'seat-'+a+'-'+(ii==''?(aa+'-empty-'+a):(ii+'-'+aa))" v-for="(ii,aa) in i" @click="ii!=''&&handleClick($event.target.dataset.key,21-a,ii,aa,a)" :class="{seat:!isNaN(ii)&&ii!=='',space:ii=='',seatA:isSeatA(21-a,ii,aa,a),booked:ii!=''&&alreadyBooked.indexOf(21-a+'-'+ii)!=-1,selected:ii!=''&&selected.indexOf(21-a+'-'+ii)!=-1}" :data-key="(21-a)+'-'+(ii==''?'space':ii)">
                        {{ii==''?'&nbsp;':ii}}
                    </div>
                </div>
                <div class="front">
                    <div class="left-wall">墙</div>
                    <div class="front-show">舞台</div>
                    <div class="right-wall">墙</div>
                </div>
                <div class="example">
                    <div style="color:red">这张票包含一个<b>{{isVip==0?'普通':'VIP'}}</b>座位</div>
                    <div style="color:red">您可以在可用座位中自行选择</div>
                    <br/>
                    <div>Tips:可以左右滑动哦</div>
                    <div class="ex-one"><div class="seat">N</div>普通座位</div>
                    <div class="ex-one"><div class="seat seatA">V</div>VIP座位</div>
                    <div class="ex-one"><div class="seat booked">B</div>别人订了</div>
                    <div class="ex-one"><div class="seat selected">S</div>自己选了</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ajax from 'djax';
export default {
    data:function(){
        window.seatACache=[];
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
            dialogVisible:true,
            isVip:this.$route.query.level=="vip",
        appZoom:z,
        loading:false,
            seatACache:[],
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
                    ],
            alreadyBooked:[
                '1-2','1-3'    
            ],selected:[  
            ],
        }},methods:{
            isSeatA:function(line,v,key,rLine){
                if(v=='')return false;
                if(line>13)return false;
                var ckey=[line,v,key,rLine].join("-");
                if(window.seatACache.indexOf(ckey)!==-1)return true;
                var a=this.seatMap[rLine].concat(['']);
                var ix=0;
                while(a[0]==''){ix++;a.shift();}
                if(a.join(' ').trim().split(" ").indexOf("")+ix>key)return  false;
                while(a[0]!=''){ix++;a.shift();}
                while(a[0]==''){ix++;a.shift();}
                while(a[0]!=''){ix++;a.shift();}
                if(key>ix)return  false;
                window.seatACache.push(ckey);
                return true;
            },handleClick:function(n,line,v,key,rLine){
                if(this.alreadyBooked.indexOf(n)!=-1)return false;
                var a=this.selected.indexOf(n);
                if(a!=-1)return this.selected.splice(a,1);
                if(this.selected.length!=0)return this.$message({
                    message: '别太贪心哪，只能选一个的',
                    type: 'info',
                    center: true,
                });
                var isSeatA=this.isSeatA(line,v,key,rLine)
                if(this.isVip==0&&isSeatA==true)return this.$message({
                    message: '还需要多多氪金！',
                    type: 'info',
                    center: true,
                });
                if(this.isVip==1&&isSeatA==false)return this.$message({
                    message: '您可是高贵的VIP用户！',
                    type: 'info',
                    center: true,
                });
                this.selected.push(n);
            },submitSeat:function(){
                var that=this;
                ajax({
                    url:$API+'seat/choose/'+this.$route.params.id,
                    data:{seat:this.selected[0]},
                    xhrFields: {withCredentials: true},
                }).done(function(e){
                    that.$message({
                        message: '选座成功！',
                        type: 'success',
                        center: true,
                    });
                    that.$router.push("/home");
                }).fail(function(e,f,g){
                    try{
                        var j=JSON.parse(g);
                        that.$alert(j.text||j.msg||"未知错误"+g, '选座失败');
                    }catch(e){
                        that.$alert('是我服务器炸了，还是你网络连不上了？', '网络错误');
                    }
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
        url:$API+'seat/occupied',
        xhrFields: {withCredentials: true},
    }).done(function(e){
        that.alreadyBooked=e.data;
    })
}
</script>
<style>
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
    z-index: -1;
}
.example .seat{
    text-align: center;
}
.el-message {
    margin-top: 30px;
} 
</style>