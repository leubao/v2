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