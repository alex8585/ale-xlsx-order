<?php

 global $wp;
 
?>

<?php if($messages): ?>
 <?php foreach($messages as $message ): ?>
     <div class="success-msg alert alert-success" role="alert"> <?php echo $message ?> </div>
 <?php endforeach; ?>
<?php endif; ?>

<?php
    
    if ($rows): 
        

?>
<table  class="products-table tablepress">
    <col class="col1">
    <col class="col2">
    <thead>
        <tr>
            <th>
                <div>№ п/п.</div>
            </th>
            <th>
                <div>Наименование продукции</div>
            </th>
            <th>
                <div>Цена без НДС, руб.</div>
            </th>
            <th>
                <div>Ставка НДС, % </div>
            </th>
            <th>
                <div>Сумма НДС, руб.</div>
            </th>
            <th>
                <div>Цена с НДС, руб.</div>
            </th>
            <th>
                <div>Вес</div>
            </th>
            <th>
                <div>Количество</div>
            </th>
            <th>
                <div>Сумма, руб.</div>
            </th>
        </tr>
    </thead>
   
    
    <tbody class="row-hover">
        <?php 
            $row_class = 'odd';
            $i=0; 
         
            $nds =  $rows[0]->nds *100;
            foreach($rows as $r): 
                $i++;
                if($i%2==0) {
                   
                    $row_class = 'odd';
                } else {
                    $row_class = 'even';
                } 
            ?>
            <tr class="product-row <?php echo $row_class?>">
                <td class="td-row-id" style="display:none;" row_id="<?php echo $r->id?>" ></td>

                <td><?php echo $r->number ?></td>
                <td  class='row-name'><?php echo $r->name ?> </td>
                <td class='td-price'  price="<?php echo $r->price?>" ><?php echo number_format($r->price, 2, '.', '') ?> </td>
                <td><?php echo ($r->nds*100 )?> </td>
                <td>
                    <?php
                        $nds_sum =  $r->price * $r->nds;
                        echo number_format($nds_sum, 2, '.', '');
                    ?> 
                </td>
                <td class='td-price-nds' price="<?php echo $r->price_nds?>">
                    <?php  echo number_format($r->price_nds, 2, '.', ''); ?> 
                </td>
                <td class='td-weight' weight="<?php echo $r->weight?>">
                    <?php  echo $r->weight; ?> 
                </td>
                <td>
                    <input class='quantity-input' type ='number' min="0" max="1000"> 
                </td>
                <td class="td-row-total" quantity='0' price_nds="" price="" weight=""></td>
            </tr>
        <?php endforeach;?>

    </tbody>
</table>





   
<div class="order-form-container">

    <div class="order-form-wrapper">

        <div style="display:none" class="error-msg alert alert-danger" role="alert">
                
        </div>

        <?php if($errors): 	?>
            <?php foreach($errors->get_error_messages() as $error ): ?>
                <div class="php-error alert alert-danger" role="alert"> <?php echo $error ?> </div>
            <?php endforeach; ?>
        
        <?php endif; ?>

        <form  method="post" enctype="multipart/form-data" class="order-form" action="<?php echo $redirect; ?>">
            <input type="hidden" name="action" value='post_order_form' >
            <input type="hidden" name="redirect" value='<?php echo $redirect; ?>' >
            
            <label for="company-name">Наименование предприятия:<span class='req'> *<span></label>
            <input type="text" name="company_name" id="company-name" >

            <label for="mail">E-mail:<span class='req'> *<span></label>
            <input type="email" name="mail" id="mail">

            <label for="phone">Телефон:<span class='req'> *<span></label>
            <input name="phone" type="text" id="phone" />

            <label for="requisite">Реквизиты:</label>
            <textarea  name="requisite" id="requisite" ></textarea>

            <label for="requisite-file">Реквизиты файл:</label>
            <div>
                <input name="requisite_file" type="file" id="requisite-file" />
            </div>
            

            
            <div>
            <label for="additionally">Дополнительно:</label>
            <textarea  name="additionally" id="additionally" ></textarea><br>
            </div>
            


            <div class='submit-btn-wrapper'>
                <button class="submit-btn btn btn-primary">Оформить заявку</button>
            </div>
        </form>

    </div>

    <div class='total-wrapper'>
        <div>
            <span>Сумма без НДС</span>
            <span class="order-total">0.00</span> 
            <span>руб.</span>
        </div>
        <div>
            <span >НДС</span>
            <span class="order-nds"><?php echo $nds?></span> 
            <span>%</span>
        </div>
        <div>
            <span>Сума с НДС</span>
            <span class="order-total-nds">0.00</span>
            <span>руб.</span>
        </div>
        <div>
            <span class="">вес</span>
            <span class="order-total-weight">0</span> 
        </div>
    </div>

    
</div>

<?php else: ?> 
<div style='text-align:center;'>Нет товаров в базе</div>
<?php endif;
