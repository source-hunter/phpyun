//获取地图坐标
function getmaplnglat(id,x,y,xid,yid){
	var data=get_map_config();
	var config=eval('('+data+')');
	var rating,map_control_type,map_control_anchor;
	if(!x && !y){x=config.map_x;y=config.map_y;}
	var map = new BMap.Map(id, {defaultCursor: 'default'});
	var pront=map.centerAndZoom(new BMap.Point(x,y),15);
	var TILE_SIZE = 256;
	map.enableScrollWheelZoom(true); 
	var opts = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	map.addControl(new BMap.NavigationControl(opts));
	
	if(config.map_control_scale==1){//比例尺
		 var opts = {offset:new BMap.Size(150,5)} 
		 map.addControl(new BMap.ScaleControl(opts));  
	 }
	
	map.addEventListener('click', function(e){
		var info = new BMap.InfoWindow('', {width: 260});
		var projection = this.getMapType().getProjection();
		var lngLat = e.point;
		document.getElementById(xid).value=lngLat.lng;//X写入到隐藏框中
		document.getElementById(yid).value=lngLat.lat;//Y写入到隐藏框中
		map.clearOverlays();//清除之前覆盖物
		var point = new BMap.Point(lngLat.lng,lngLat.lat);
		var marker = new BMap.Marker(point);  // 创建标注
		map.addOverlay(marker);           // 将标注添加到地图中
	});
}

//在标准上展示内容
function getmapshowcont(id,x,y,title,cont){
	var map = new BMap.Map(id);
	var point = new BMap.Point(x,y);
	var marker = new BMap.Marker(point);
	map.enableScrollWheelZoom(true); 
	var optsa = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	map.addControl(new BMap.NavigationControl(optsa)); 
	var opts = {
	  width : 50,     // 信息窗口宽度
	  height: 20,     // 信息窗口高度
	  title : title  // 信息窗口标题
	}
	map.centerAndZoom(point, 15);
	map.addOverlay(marker);
	var infoWindow = new BMap.InfoWindow(cont, opts);  // 创建信息窗口对象
	marker.openInfoWindow(infoWindow);  
	marker.addEventListener("click", function(){      //点击触发 
	marker.openInfoWindow(infoWindow);    
	});
}

//getmapshowcont('map_container',116.404, 39.915,'ddd','aaaa');
//在标准上不展示内容 getmapnoshowcont
function getmapnoshowcont(id,x,y,xid,yid){
	var data=get_map_config();
	var config=eval('('+data+')');
	var rating,map_control_type,map_control_anchor;
	if(!x && !y){x=config.map_x;y=config.map_y;}
	
	
	
	var map = new BMap.Map(id);
	var point = new BMap.Point(x,y);
	var marker = new BMap.Marker(point);
	var opts = {type:BMAP_NAVIGATION_CONTROL_LARGE} 
	//根据IP到城市开始
	var myCity = new BMap.LocalCity();
	myCity.get(myFun);
	//根据IP到城市结结束
	map.enableScrollWheelZoom(true); 
	map.addControl(new BMap.NavigationControl(opts));  
	map.centerAndZoom(point, 15);
	map.addOverlay(marker);	
	map.addEventListener("click",function(){
	   getmaplnglat(id,x,y,xid,yid);
	});
	return map;
}
//getmapnoshowcont('map_container',116.404,39.915);
function myFun(result){
	var cityName = result.name;
	map.setCenter(cityName);
}
function get_map_config(){
	var config="";
	$.ajax( {
			async : false,
			type : "post",
			url :'../index.php?m=ajax&c=mapconfig',
			data : {id:""},
			success : function(set) {
			config=set;
		}
		});
	return config;
}