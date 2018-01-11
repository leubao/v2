<?
//根据日期计算生日
function getAgeByID($id){ 
	//过了这年的生日才算多了1周岁 
    if(empty($id)) return ''; 
    $date = strtotime(substr($id,6,8));
	//获得出生年月日的时间戳 
    $today = strtotime('today');
	//获得今日的时间戳 111cn.net
    $diff = floor(($today-$date)/86400/365);
	//得到两个日期相差的大体年数 
	//strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比 
    $age = strtotime(substr($id,6,8).'+'.$diff.'years') > $today ? ($diff+1) : $diff;
    echo $age; 
}
//获取活动
function getActivity($id,$type = '')
{
    $info = D('Activity')->where('id',$id)->field('id,title')->find();
    if($type){
        return $info;
    }else{
        echo $info['title'];
    }
}