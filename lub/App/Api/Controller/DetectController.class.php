<?php
/*
 * 检票程序
 * zj
 * 2013-10-15
 */
namespace Api\Controller;
use Think\Controller;

use Code\Service\Codeapi;
use Libs\Service\Operate;
use Libs\Service\Checkin;
class DetectController extends Controller{

	
	function index(){
		$info = D('Item/Order')->where(array('order_sn'=>'70314158721843'))->find();
		$info['info'] = unserialize($info['info']);
		dump($info);
		/*
		if(!empty($jp_id)){
			$this->redirect('index.php?g=Api&m=Detect&a=j_p');
		}else {
			$this->display();
		}*/
	}
	function j_p(){
		$this->display();
		
	}
	/*检票客户端注册
	 * post 传递过来的参数
	 * itemsn 6位
	 * 422: 提交方式错误
	 * 433:商户识别号错误
	 * Ok：成功
	 * */
	function itre(){
		if (IS_POST) {
			$item=I('post.');
			if(!empty($item['code'])){
				$where['idcode']=$item['code'];
				$status=D('Terminal')->where($where)->find();
				if($status){
					//判断是否是新终端
					if (empty($status['imei'])) {
						$ttid=M("Terminal")->save(array('id'=>$status['id'],'imei'=>$item['mac'],'uptime' => time()));
						F('Terminal_'.$item['code'],$status);
						$data=array(
							'state'=>"OK",
							'data' =>array('tid'=>$ttid,"msg"=>"认证成功"),
						);
						$this->recordLogindetect($item['code'], 1,1,$item,$data);
					  	echo json_encode($data);
					} else {
						if($item['mac'] == $status['imei']){
							M("Terminal")->where('id='.$status['id'])->setField('uptime',time());
							F('Terminal_'.$item['code'],$status);
							$data=array(
								'state'=>"OK",
								'data' =>array("msg"=>"认证成功"),
							);
						}else{
							$data=array(
								'state'=>"433",
								'data' =>array("msg"=>"认证失败"),
							);
						}
						$this->recordLogindetect($item['code'], 1,1,$item,$data);
					  	echo json_encode($data);
					}
				} else {
					$data=array(
						'state'=>"433",
						'data' =>array("msg"=>"无效识别码"),
					);
					$this->recordLogindetect($item['code'], 1,0,$item,$data);
				  	echo json_encode($data);
				}
			} else {
				$data=array(
					'state'=>"433",
					'data' =>array("msg"=>"请求数据错误"),
				);
				$this->recordLogindetect($item['code'], 1,0,$item,$data);
				echo json_encode($data);
			}
		} else {
			$data=array(
				'state'=>"422",
				'data' =>array("msg"=>"请求方式有误"),
			);
			$this->recordLogindetect($item['code'], 1,0,$item,$data);
		  	echo json_encode($data);
		}	
	}
	
	
	/*新检票方法
	*
	*/
	function mobile_check_in(){
		if(IS_POST){
			$pinfo = I('post.');
			if(empty($pinfo['code'])){
				$data=array(
					'state'=>"102",
					'data' =>array('msg'=>"请求数据错误"),
				);
				$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
				echo json_encode($data);
			}
			$terminal = F('Terminal_'.$pinfo['code']);
			if($terminal['product_type'] == '1'){

				//剧院
				/*if($this->games($terminal['product_id']) == false){
					$data=array(//检票服务终止
						'state'=>"444",
						'data' =>array('msg'=>"检票服务终止"),
					);
					$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
					echo json_encode($data);
				}else{
					$this->jp($pinfo['sn'],$pinfo['code']);
				}*/

				if(Checkin::checkin($pinfo['code'],$pinfo['sn'])){
					$data=array(
						'state'=>"99",
						'data' =>array('msg'=>"成功"),
					);
					$this->recordLogindetect($code, 1,1,$sn,$data);
					echo json_encode($data);
					return true;
				}else{
					$data=array(
						'state'=>"201",
						'data' =>array('msg'=>"门票状态更新失败"),
					);
					$this->recordLogindetect($code, 1,0,$sn,$data);
			  		echo json_encode($data);
			  		return false;
				}

			}else{
				//景区
			}
		}else{
			$data=array(//未经许可的请求
				'state'=>"111",
				'data' =>array('msg'=>"未经许可客户端"),
			);
			$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
			echo json_encode($data);
		}
	}


	/**
	 * 监票
	 */
	function prison_ticket(){
		if(IS_PSOT){
			$pinfo = I('post.');
			if(empty($pinfo['code'])){
				$data=array(
					'state'=>"102",
					'data' =>array('msg'=>"请求数据错误"),
				);
				$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
				echo json_encode($data);
			}else{
				$terminal = F('Terminal_'.$pinfo['code']);
				if($terminal['product_type'] == '1'){
					//剧院
					$sn = $this->split($pinfo['sn']);
					if($sn != false){
						$plan = Operate::do_read('Plan',0,array('id'=>$sn['0']));
						if(!empty($plan)){
							$info = $this->ticket_info($plan['seat_table'],$sn['1'],2);//获取订单信息
							$state = \Libs\Service\Encry::decryption($sn['0'],$info['order_sn'],$plan['encry'],$info['area'],$info['seat'],$info['print'],$sn['1'],$sn['2']);
							if($state){
								$info['sale'] = unserialize($info['sale']);
				            	if(!empty($info)){
				               		$data=array(
										'state'=>"OK",
										'data' =>array(
											'sn'		=>	$info['order_sn'],
											'plan'		=>	planShow($sn['0'],1),
											'product'	=>	productName($plan['product_id']),
											'area'		=>	$info['sale']['area'],
											'price'		=>	$info['sale']['price'],
											'ticketname'=>  $info['sale']['priceName'],
											'seat'		=>	$info['sale']['seat'],
											'status'	=>	$info['status'],
											'nums'		=>	1,
										),
									);
									$this->recordLogindetect($pinfo['code'], 1,1,$pinfo,$data);
							  		echo json_encode($data);
				               }else{
				               		$data=array(
										'state'=>"201",
										'data' =>array('msg'=>"未找到定单"),
									);
									$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
							  		echo json_encode($data);
				               }
							}else{
								$data=array(
									'state'=>"202",
									'data' =>array('msg'=>"订单校验失败"),
								);
								$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
							  	echo json_encode($data);
							}
						}else{
							$data=array(
								'state'=>"401",
								'data' =>array('msg'=>"读取销售计划失败"),
							);
							$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
							echo json_encode($data);
						}
					}else{
						$data=array(
							'state'=>"202",
							'data' =>array('msg'=>"订单号解析失败"),
						);
						$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
					  	echo json_encode($data);		
					}
				}else{
					//景区
					$data=array(
						'state'=>"201",
						'data' =>array('msg'=>"景区门票"),
					);
					$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
					echo json_encode($data);
				}
			}
		}else{
			$data=array(//未经许可的请求
				'state'=>"111",
				'data' =>array('msg'=>"未经许可客户端"),
			);
			$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
			echo json_encode($data);
		}		
	}
	/*
	 * 闸机检票
	 * 返回状态码
	 * 0：订单号未获取
	 * 111：未授权客户端
	 * 101：未完成订单（未完成付款）
	 * 105: 未经许可的请求
	 * 201：未找到订单（该该门票）
	 * 301：已使用门票 
	 * 401：非当日门票/非当日门票、过期门票
	 * 411：门票已过期
	 * 99：通过 OK：通过
	 * */
	function check(){
		if(IS_POST){
			$info = I('post.');//load_redis('setex','211221',json_encode($info),'36000');
			if(Checkin::checkin($info['header'],$info['content'])){
				echo "1";
				return true;
			}else{
				echo "0";
				return false;
			}
		}else{
			echo "0";
			return false;
		}	
	}
	/*
	 * 检票
	 * $sn 订单号
	 * */
	function jp($sn,$code){
		$sn = $this->split($sn);
		$plan = F('Plan_'.$sn['0']);
		if(empty($plan)){
			$data=array(
				'state'=>"401",
				'data' =>array('msg'=>"读取销售计划失败"),
			);
			$this->recordLogindetect($code, 1,0,$sn,$data);
			echo json_encode($data);
		}else{
			$info = $this->ticket_info($plan['seat_table'],$sn['1']);//获取订单信息
			if(!empty($info)){
				$state = \Libs\Service\Encry::decryption($sn['0'],$info['order_sn'],$plan['encry'],$info['area'],$info['seat'],$info['print'],$sn['1'],$sn['2']);
				if($state){
					//更新检票
					if($this->up_seat($plan['seat_table'],$info)){
						$data=array(
							'state'=>"99",
							'data' =>array('msg'=>"成功"),
						);
						$this->recordLogindetect($code, 1,1,$sn,$data);
						echo json_encode($data);
					}else{
						$data=array(
							'state'=>"201",
							'data' =>array('msg'=>"门票状态更新失败"),
						);
						$this->recordLogindetect($code, 1,0,$sn,$data);
			  			echo json_encode($data);
					}
				}else{
					$data=array(
					'state'=>"202",
					'data' =>array('msg'=>"订单校验失败"),
				);
				$this->recordLogindetect($pinfo['code'], 1,0,$pinfo,$data);
			  	echo json_encode($data);
				}

			}else{
				$data=array(
					'state'=>"201",
					'data' =>array('msg'=>"未找到定单"),
				);
				$this->recordLogindetect($code, 1,0,$sn,$data);
		  		echo json_encode($data);
			}
		}
		
	}
	/*
	*获取门票信息
	*@param $table 表名称
	*@param $seat_id 座位id
	*@param $type 请求方法 1　检票　　2：为监票
	*/
	function ticket_info($table,$seat_id,$type = '1'){
		$map = array(	
			'id' =>	$seat_id,
		);
		if($type == '1'){
			$map['status'] = '2';
		}
		$info = M(ucwords($table))->where($map)->find();
		return $info;
	}

	/*
	* 更新座椅状态  用于检票
	* @param $table string 表名称
	* @param $info  array 座位信息更新条件
	*/
	function up_seat($table,$info){
		$map = array(
			'order_sn'	=>	$info['order_sn'],
			'area'		=>	$info['area'],
			'seat'		=>	$info['seat'],
			'print'		=>	$info['print'],	
			'id'		=>	$info['id'],
			'status'	=>	'2',
		);
		$status = M(ucwords($table))->where($map)->save(array('status'=>'99','checktime'=>time()));
		return $status;
	}
	/*
	* 订单号拆解
	* @param $sn string 待拆分的订单号
	*/
	public function split($sn){
		if(!empty($sn)){
			$sns = explode('^', $sn);
			return $sns;
		}
	}
	
	/**
	 * 订单号还原
	 * @param $sn string 加密的订单号
	 * @param $encry string 加密常量
	 */
	function restore($sn,$encry){
		if(!empty($sn)){
			$info = \Libs\Util\Encrypt::authcode($sn, DECODE,$encry);
			$info = explode(',', $info);
			return $info;
		}
	}
	/**
     * 记录检票终端信息
     * @param $code 识别号
     * @param $type 监票终端类型
     * @param $status 状态
     * @param $info 请求数据
     * @param $data 返回数据
     */
    public function recordLogindetect($code, $type, $status, $info = "" , $data = "") {
        M("Checklog")->add(array(
            "code" => $code,
        	'type' => $type,
            "datetime" => time(),
            "ip" => get_client_ip(),
            "status" => $status,
            "info" => serialize($info),
        	"data" => serialize($data),
        ));
    }

	/**
	 * 临时功能查看电子票
	 */
    function ticket(){
    	$sn = I('get.sn');
    	$info = D('Item/Order')->where(array('order_sn'=>$sn))->find();
    	if(!empty($info)){
    		//读取计划
    		$plan = M('Plan')->where(array('id'=>$info['plan_id']))->find();
    		//查询座位表
    		$list = M(ucwords($plan['seat_table']))->where(array('order_sn'=>$sn))->select();
    		//返回信息
    		
    	}
    	$config = cache("Config");
    	foreach ($list as $ke=>$va){
    		$print = $va['print']+1;
    		/*$ec = $va['order_sn'].','.$va['area'].','.$va['seat'].','.$print.','.$va['id'];
    		
    		$data[$ke]['data'] = $plan['id']."^".\Libs\Util\Encrypt::authcode($ec,ENCODE,$plan['encry'])."^#";*/
    		$data[$ke]['data'] = $this->re_print($plan['id'],$plan['encry'],$va);
	    	$b= Codeapi::code_create($data[$ke]['data'],$config['code_size'],$config['level'],1,$config['q_color'],$config['b_color'],$config['w_color'],$config['n_color'],'','');
	    	/*图片转base64*/
	    	$type=getimagesize($b);//取得图片的大小，类型等 
	    	$fp=fopen($b,"r")or die("Can't open file"); 
	    	$data[$ke]['img'] = chunk_split(base64_encode(fread($fp,filesize($b))));//base64编码
	    	//更新打印次数
	    	M(ucwords($plan['seat_table']))->where(array('id'=>$va['id']))->setInc('print',1);
    	}
    	$a= "2A5500FC56123400000001"."155^e735yIt40TW/t6ff8FFUBd9corYssxj18g1z8gPJnU+aj7VAQ4wQRGjsqvm9n+j4ni8r4nVQEHlMPw#";
    	$t= "2A5500FC561234000000013135355E3664336233326F356561396A755336494377773071646F5753452B4E6253524D366E655541666C39437A543532736974576757456E5130736A7737586B37446744595250434F526B72464D4C6E775E2300140E0BFD";
    	//$cc = "2A5500FC561234000000013135355E3664336233326F356561396A755336494377773071646F5753452B4E6253524D366E655541666C39437A543532736974576757456E5130736A7737586B37446744595250434F526B72464D4C6E775E2300";
    	$bb = "*03000000013135355E3664336233326F356561396A755336494377773071646F5753452B4E6253524D366E655541666C39437A543532736974576757456E5130736A7737586B37446744595250434F526B72464D4C6E775E2300";
    	$io = "*5400000001208^f821bOOeM4fdsOxdLcdOkFvpZWllgdji9h+qQhe/qjVMC+zOcZBhH8TmVxABnjiY7+F4c2gIh0Vmlg^#";
    	$rr = $this->reader($io);
 
    	echo "16进制".$rr."<br />";
    	//echo "16进制".dechex($rr);
    	$this->assign('data',$data);
    	$this->display();
    }
    /*
	返回打印数据
	$plan_id 计划id
	$encry 加密常量
	$data 待处理的数据
	*/
	private function re_print($plan_id,$encry,$data){
		$print = $data['print']+1;
		$code = \Libs\Service\Encry::encryption($plan_id,$data['order_sn'],$encry,$data['area'],$data['seat'],$print,$data['id']);
		//$code = $v['order_sn'].','.$v['area'].','.$v['seat'].','.$print[$k].','.$v['id'];
		$sn = $code."^#";
		//$info = array_merge(array('sn' => $sn,'sns'=>$data['order_sn']),unserialize($data['sale']));
		return $sn;
	}
    //校验
    function reader($info){
    	dump($info);
    	$header = substr($info,0,11);
    	$data = substr($info,11);
    	//$info = $header.$this->hexToStr($data);
    	$info = $header.$data;
    	$len = strlen($info);
    	for ($i=0; $i < $len; $i++) { 
    	 	$code = $code+(int)ord($info[$i]);
    	}
    	$code = $code%256;
    	$code = dechex($code);
    	return $code;
    }
    //十六进制转字符串
    function hexToStr($hex){   
		$string=""; 
		for($i=0;$i<strlen($hex)-1;$i+=2){
			$string.=chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	//字符串转十六进制
	function strToHex($string){ 
		$hex="";
		for($i=0;$i<strlen($string);$i++){
			$hex.=dechex(ord($string[$i]));
		}
		$hex=strtoupper($hex);
		return $hex;
	}

	function curl_server($header,$content){
	    $url = "http://new.chengde360.com/index.php?g=Api&m=Detect&a=check";//请求地址
	    $post_data = array('header'=>$header,'content'=>$content);
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch,CURLOPT_POST,1);
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}
}