<?php
class TicketTypeViewModel extends ViewModel {
   public $viewFields = array(
     'TicketType'=>array('id','price'),
     'Area'=>array('id'=>'areaid','areaname','_on'=>'TicketType.area=Area.id'),
   );
}

?>