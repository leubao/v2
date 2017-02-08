<!--商户详情页面-->  
  <table class="table" width="100%" layoutH="0">
    <tr>
      <td>姓名</td>
      <td>{$data.nickname}</td>      
    </tr>
    <tr>
      <td>性别</td>
      <td>
          <if condition="$data['sex'] eq 1">
            男
          <else/>
            女
          </if>
      </td>      
    </tr>    
    <tr>
      <td>身份证号</td>
      <td>{$data.idnumber}</td>      
    </tr>    
    <tr>
      <td>电话号码</td>
      <td>{$data.phone}</td>      
    </tr>
    <tr>
      <td>相关销售</td>
      <td>{$data.salesman}</td>      
    </tr>
    <tr>
      <td>微信</td>
      <td>{$data.wechat}</td>      
    </tr>
    <tr>
      <td>微博</td>
      <td>{$data.weibo}</td>      
    </tr>
    <tr>
      <td>邮箱</td>
      <td>{$data.email}</td>      
    </tr>            
    <!-- <tr>
      <td>银行账号</td>
      <td>{$data.bank_account}</td>      
    </tr>
    <tr>
      <td>开户行</td>
      <td>{$data.bank}</td>      
    </tr> -->
    <if condition="$data['cardid'] neq ''">
      <tr>
        <td>导游证号</td>
        <td>{$data.cardid}</td>      
      </tr>      
    </if>      
    <tr>
      <td>状态</td>
      <td>
        <if condition="$data['status'] eq 1">
          启用
        <else/>  
          不启用
        </if>
      </td>      
    </tr>                              
    <tbody>

    </tbody>
  </table>
