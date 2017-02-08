<!--商户详情页面-->  
  <table class="table" width="100%" layoutH="0">
    <tr>
      <td>客户名称</td>
      <td>{$data.name}</td>      
    </tr>
    <if condition="$data['address'] neq ''">
      <tr>
        <td>地址</td>
        <td>{$data.address}</td>      
      </tr>      
    </if>
    <tr>
      <td>联系人</td>
      <td>{$data.contacts}</td>      
    </tr>
    <tr>
      <td>联系人电话</td>
      <td>{$data.phone}</td>      
    </tr>
    <tr>
      <td>相关销售</td>
      <td>{$data.salesman}</td>      
    </tr>
    <if condition="$data['tariff'] neq ''">
      <tr>
        <td>税号</td>
        <td>{$data.tariff}</td>      
      </tr>
      <tr>
              <td>银行账号</td>
              <td>{$data.bank_account}</td>      
            </tr>
            <tr>
              <td>开户行</td>
              <td>{$data.bank}</td>      
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