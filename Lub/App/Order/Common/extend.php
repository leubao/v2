<?

//获取活动
function getActivity($id,$type = '')
{
    $info = D('Activity')->where(['id'=>$id])->field('id,title')->find();
    if($type){
        return $info;
    }else{
        echo $info['title'];
    }
}
//获取会员入园记录
function thetype($value='')
{
	switch ($value) {
		case '1':
			$return = '身份证';
			break;
		case '2':
			$return = '临时凭证';
			break;
	}
	echo $return;
}
//入园状态
function minto($param='')
{
	switch ($param) {
	    case 0:
	        echo "<span class='label label-danger'>已作废</span>";
	        break;
	    case 1:
	        echo "<span class='label label-info'>待入园</span>";
	        break;
	    case 9:
	        echo "<span class='label label-default'>已入园</span>";
	        break;
	}
}
//会员id
function memberName($value='')
{
	$info = D('Member')->where(['id'=>$value])->field('nickname')->find();
	echo $info['nickname'];
}